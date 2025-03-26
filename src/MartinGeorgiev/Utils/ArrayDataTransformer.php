<?php

declare(strict_types=1);

namespace MartinGeorgiev\Utils;

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
     */
    public static function transformPostgresTextArrayToPHPArray(string $postgresArray): array
    {
        $trimmed = \trim($postgresArray);

        if ($trimmed === '' || \strtolower($trimmed) === self::POSTGRESQL_NULL_VALUE) {
            return [];
        }

        if (\str_contains($trimmed, '},{') || \str_starts_with($trimmed, '{{')) {
            throw new \InvalidArgumentException('Only single-dimensioned arrays are supported');
        }

        if ($trimmed === self::POSTGRESQL_EMPTY_ARRAY) {
            return [];
        }

        $jsonArray = '['.\trim($trimmed, '{}').']';

        /** @var array<int, mixed>|null $decoded */
        $decoded = \json_decode($jsonArray, true, 512, JSON_BIGINT_AS_STRING);
        if ($decoded === null && \json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid array format: '.\json_last_error_msg());
        }

        return \array_map(
            static fn (mixed $value): mixed => \is_string($value) ? self::unescapeString($value) : $value,
            (array) $decoded
        );
    }

    /**
     * This method supports only single-dimensioned PHP arrays.
     * This method relays on the default escaping strategy in PostgreSQL (double quotes).
     */
    public static function transformPHPArrayToPostgresTextArray(array $phpArray): string
    {
        if ($phpArray === []) {
            return self::POSTGRESQL_EMPTY_ARRAY;
        }

        if (\array_filter($phpArray, 'is_array')) {
            throw new \InvalidArgumentException('Only single-dimensioned arrays are supported');
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

        // Handle objects that implement __toString()
        if (\is_object($value)) {
            if (\method_exists($value, '__toString')) {
                $stringValue = $value->__toString();
            } else {
                // For objects without __toString, use a default representation
                $stringValue = $value::class;
            }
        } else {
            // For all other types, force string conversion
            // This covers strings, resources, and other types
            $stringValue = match (true) {
                \is_resource($value) => '(resource)',
                default => (string) $value // @phpstan-ignore-line
            };
        }

        \assert(\is_string($stringValue));

        // Handle empty string
        if ($stringValue === '') {
            return '""';
        }

        if (self::isNumericSimple($stringValue)) {
            return '"'.$stringValue.'"';
        }

        // Double the backslashes and escape quotes
        $escaped = \str_replace(
            ['\\', '"'],
            ['\\\\', '\"'],
            $stringValue
        );

        return '"'.$escaped.'"';
    }

    private static function isNumericSimple(string $value): bool
    {
        // Fast path for obvious numeric strings
        if ($value === '' || $value[0] === '"') {
            return false;
        }

        // Handle scientific notation
        $lower = \strtolower($value);
        if (\str_contains($lower, 'e')) {
            $value = \str_replace('e', '', $lower);
        }

        // Use built-in numeric check
        return \is_numeric($value);
    }

    private static function unescapeString(string $value): string
    {
        // First handle escaped quotes
        $value = \str_replace('\"', '___QUOTE___', $value);

        // Handle double backslashes
        $value = \str_replace('\\\\', '___DBLBACK___', $value);

        // Restore double backslashes
        $value = \str_replace('___DBLBACK___', '\\\\', $value);

        // Finally restore quotes
        return \str_replace('___QUOTE___', '"', $value);
    }
}
