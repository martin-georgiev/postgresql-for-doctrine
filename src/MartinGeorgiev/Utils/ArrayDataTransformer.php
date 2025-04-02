<?php

declare(strict_types=1);

namespace MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\Exception\InvalidArrayFormatException;

/**
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ArrayDataTransformer
{
    private const POSTGRESQL_EMPTY_ARRAY = '{}';

    private const POSTGRESQL_NULL_VALUE = 'null';

    /**
     * This method supports only single-dimensioned text arrays and
     * relays on the default escaping strategy in PostgreSQL (double quotes).
     *
     * @throws InvalidArrayFormatException when the input is a multi-dimensional array or has invalid format
     */
    public static function transformPostgresTextArrayToPHPArray(string $postgresArray): array
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
        $content = \trim($trimmed, '{}');
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
        $jsonArray = '['.\trim($trimmed, '{}').']';

        /** @var array<int, mixed>|null $decoded */
        $decoded = \json_decode($jsonArray, true, 512, JSON_BIGINT_AS_STRING);

        // If json_decode fails, try manual parsing for unquoted strings
        if ($decoded === null && \json_last_error() !== JSON_ERROR_NONE) {
            return self::parsePostgresArrayManually($content);
        }

        return \array_map(
            static fn (mixed $value): mixed => \is_string($value) ? self::unescapeString($value) : $value,
            (array) $decoded
        );
    }

    /**
     * Manually parse a PostgreSQL array content string.
     */
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

    /**
     * Check if the value is a NULL value.
     */
    private static function isNullValue(string $value): bool
    {
        return $value === 'NULL' || $value === 'null';
    }

    /**
     * Check if the value is a boolean value.
     */
    private static function isBooleanValue(string $value): bool
    {
        return \in_array($value, ['true', 't', 'false', 'f'], true);
    }

    /**
     * Process a boolean value.
     */
    private static function processBooleanValue(string $value): bool
    {
        return $value === 'true' || $value === 't';
    }

    /**
     * Check if the value is a quoted string.
     */
    private static function isQuotedString(string $value): bool
    {
        return \strlen($value) >= 2 && $value[0] === '"' && $value[\strlen($value) - 1] === '"';
    }

    /**
     * Process a quoted string.
     */
    private static function processQuotedString(string $value): string
    {
        // Remove the quotes and unescape the string
        $unquoted = \substr($value, 1, -1);

        return self::unescapeString($unquoted);
    }

    /**
     * Check if the value is a numeric value.
     */
    private static function isNumericValue(string $value): bool
    {
        return \is_numeric($value);
    }

    /**
     * Process a numeric value.
     */
    private static function processNumericValue(string $value): float|int
    {
        // Convert to int or float as appropriate
        if (\str_contains($value, '.') || \stripos($value, 'e') !== false) {
            return (float) $value;
        }

        return (int) $value;
    }

    /**
     * This method supports only single-dimensioned PHP arrays.
     * This method relays on the default escaping strategy in PostgreSQL (double quotes).
     *
     * @throws InvalidArrayFormatException when the input is a multi-dimensional array or has invalid format
     */
    public static function transformPHPArrayToPostgresTextArray(array $phpArray): string
    {
        if ($phpArray === []) {
            return self::POSTGRESQL_EMPTY_ARRAY;
        }

        if (\array_filter($phpArray, 'is_array')) {
            throw InvalidArrayFormatException::multiDimensionalArrayNotSupported();
        }

        /** @var array<int|string, string> */
        $processed = \array_map(
            static fn (mixed $value): string => self::formatValue($value),
            $phpArray
        );

        return '{'.\implode(',', $processed).'}';
    }

    /**
     * Formats a single value for PostgreSQL array.
     */
    private static function formatValue(mixed $value): string
    {
        // Handle null
        if ($value === null) {
            return 'NULL';
        }

        // Handle actual numbers
        if (\is_int($value) || \is_float($value)) {
            return (string) $value;
        }

        // Handle booleans
        if (\is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (\is_object($value)) {
            if (\method_exists($value, '__toString')) {
                $stringValue = $value->__toString();
            } else {
                // For objects without __toString, use a default representation
                $stringValue = $value::class;
            }
        } elseif (\is_resource($value)) {
            $stringValue = '(resource)';
        } else {
            $valueType = \get_debug_type($value);

            if ($valueType === 'string') {
                $stringValue = $value;
            } elseif (\in_array($valueType, ['int', 'float', 'bool'], true)) {
                /** @var bool|float|int $value */
                $stringValue = (string) $value;
            } else {
                $stringValue = $valueType;
            }
        }

        \assert(\is_string($stringValue));

        // Handle empty string
        if ($stringValue === '') {
            return '""';
        }

        // Always quote strings to match the test expectations
        // Double the backslashes and escape quotes
        $escaped = \str_replace(
            ['\\', '"'],
            ['\\\\', '\"'],
            $stringValue
        );

        return '"'.$escaped.'"';
    }

    private static function unescapeString(string $value): string
    {
        // First handle escaped quotes
        $value = \str_replace('\"', '___QUOTE___', $value);

        // Handle double backslashes
        $value = \str_replace('\\\\', '___DBLBACK___', $value);

        // Restore double backslashes
        $value = \str_replace('___DBLBACK___', '\\', $value);

        // Finally restore quotes
        return \str_replace('___QUOTE___', '"', $value);
    }
}
