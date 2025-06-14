<?php

declare(strict_types=1);

namespace MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\Exception\InvalidArrayFormatException;

/**
 * Handles transformation from PostgreSQL text arrays to PHP values.
 *
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class PostgresArrayToPHPArrayTransformer
{
    private const POSTGRESQL_EMPTY_ARRAY = '{}';

    private const POSTGRESQL_NULL_VALUE = 'null';

    /**
     * Transforms a PostgreSQL text array to a PHP array.
     * This method supports only single-dimensional text arrays and
     * relies on the default escaping strategy in PostgreSQL (double quotes).
     *
     * @throws InvalidArrayFormatException when the input is a multi-dimensional array or has an invalid format
     */
    public static function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        $trimmed = \trim($postgresArray);

        if ($trimmed === '' || \strtolower($trimmed) === self::POSTGRESQL_NULL_VALUE) {
            return [];
        }

        if (\str_contains($trimmed, '},{') || \str_starts_with($trimmed, '{{')) {
            throw InvalidArrayFormatException::multiDimensionalArrayNotSupported();
        }

        if ($trimmed === self::POSTGRESQL_EMPTY_ARRAY) {
            return [];
        }

        // Check for malformed nesting - this is a more specific check than the one above
        // But we need to exclude cases where curly braces are part of quoted strings
        $content = \trim($trimmed, self::POSTGRESQL_EMPTY_ARRAY);
        $inQuotes = false;
        $escaping = false;

        for ($i = 0, $len = \strlen($content); $i < $len; $i++) {
            $char = $content[$i];

            if ($escaping) {
                $escaping = false;

                continue;
            }

            if ($char === '\\' && $inQuotes) {
                $escaping = true;

                continue;
            }

            if ($char === '"') {
                $inQuotes = !$inQuotes;
            } elseif (($char === '{' || $char === '}') && !$inQuotes) {
                throw InvalidArrayFormatException::invalidFormat('Malformed array nesting detected');
            }
        }

        // Check for unclosed quotes
        if ($inQuotes) {
            throw InvalidArrayFormatException::invalidFormat('Unclosed quotes in array');
        }

        // First try with json_decode for properly quoted values
        $jsonArray = '['.\trim($trimmed, self::POSTGRESQL_EMPTY_ARRAY).']';

        /** @var array<int, mixed>|null $decoded */
        $decoded = \json_decode($jsonArray, true, 512, JSON_BIGINT_AS_STRING);

        // If json_decode fails, try manual parsing for unquoted strings
        if ($decoded === null && \json_last_error() !== JSON_ERROR_NONE) {
            return self::parsePostgresArrayManually($content);
        }

        return (array) $decoded;
    }

    private static function parsePostgresArrayManually(string $content): array
    {
        if ($content === '') {
            return [];
        }

        // Parse the array manually, handling quoted and unquoted values
        $result = [];
        $inQuotes = false;
        $currentValue = '';
        $escaping = false;

        for ($i = 0, $len = \strlen($content); $i < $len; $i++) {
            $char = $content[$i];

            // Handle escaping within quotes
            if ($escaping) {
                $currentValue .= $char;
                $escaping = false;

                continue;
            }

            if ($char === '\\' && $inQuotes) {
                $escaping = true;
                $currentValue .= $char;

                continue;
            }

            if ($char === '"') {
                $inQuotes = !$inQuotes;
                // For quoted values, we include the quotes for later processing
                $currentValue .= $char;
            } elseif ($char === ',' && !$inQuotes) {
                // End of value
                $result[] = self::processPostgresValue($currentValue);
                $currentValue = '';
            } else {
                $currentValue .= $char;
            }
        }

        // Add the last value
        if ($currentValue !== '') {
            $result[] = self::processPostgresValue($currentValue);
        }

        return $result;
    }

    /**
     * Process a single value from a PostgreSQL array.
     */
    private static function processPostgresValue(string $value): mixed
    {
        $value = \trim($value);

        if (self::isNullValue($value)) {
            return null;
        }

        if (self::isBooleanValue($value)) {
            return self::processBooleanValue($value);
        }

        if (self::isQuotedString($value)) {
            return self::processQuotedString($value);
        }

        if (self::isNumericValue($value)) {
            return self::processNumericValue($value);
        }

        // For unquoted strings, return as is
        return $value;
    }

    private static function isNullValue(string $value): bool
    {
        return $value === 'NULL' || $value === 'null';
    }

    private static function isBooleanValue(string $value): bool
    {
        return \in_array($value, ['true', 't', 'false', 'f'], true);
    }

    private static function processBooleanValue(string $value): bool
    {
        return $value === 'true' || $value === 't';
    }

    private static function isQuotedString(string $value): bool
    {
        return \strlen($value) >= 2 && $value[0] === '"' && $value[\strlen($value) - 1] === '"';
    }

    private static function processQuotedString(string $value): string
    {
        // Remove the quotes and unescape the string
        $unquoted = \substr($value, 1, -1);

        return self::unescapeString($unquoted);
    }

    private static function isNumericValue(string $value): bool
    {
        return \is_numeric($value);
    }

    private static function processNumericValue(string $value): float|int
    {
        // Convert to int or float as appropriate
        if (\str_contains($value, '.') || \stripos($value, 'e') !== false) {
            return (float) $value;
        }

        return (int) $value;
    }

    private static function unescapeString(string $value): string
    {
        /**
         * PostgreSQL array escaping rules:
         * \\ -> \ (escaped backslash becomes literal backslash)
         * \" -> " (escaped quote becomes literal quote)
         * Everything else remains as-is
         */
        $result = '';
        $length = \strlen($value);
        $position = 0;
        
        while ($position < $length) {
            if ($value[$position] === '\\' && $position + 1 < $length) {
                $nextChar = $value[$position + 1];
                
                if ($nextChar === '\\') {
                    // \\ -> \
                    $result .= '\\';
                    $position += 2;
                } elseif ($nextChar === '"') {
                    // \" -> "
                    $result .= '"';
                    $position += 2;
                } else {
                    // \ followed by anything else - keep the backslash
                    $result .= '\\';
                    $position++;
                }
            } else {
                $result .= $value[$position];
                $position++;
            }
        }

        return $result;
    }
}
