<?php

declare(strict_types=1);

namespace MartinGeorgiev\Utils;

use MartinGeorgiev\Utils\Exception\InvalidArrayFormatException;

/**
 * Handles transformation from PHP values to PostgreSQL values.
 *
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class PHPArrayToPostgresValueTransformer
{
    private const POSTGRESQL_EMPTY_ARRAY = '{}';

    /**
     * Transforms a PHP array to a PostgreSQL text array.
     * This method supports only single-dimensioned PHP arrays.
     * This method relays on the default escaping strategy in PostgreSQL (double quotes).
     *
     * @throws InvalidArrayFormatException when the input is a multi-dimensional array or has invalid format
     */
    public static function transformToPostgresTextArray(array $phpArray): string
    {
        if ($phpArray === []) {
            return self::POSTGRESQL_EMPTY_ARRAY;
        }

        if (\array_filter($phpArray, is_array(...))) {
            throw InvalidArrayFormatException::multiDimensionalArrayNotSupported();
        }

        /** @var array<int|string, string> */
        $processed = \array_map(
            self::formatValue(...),
            $phpArray
        );

        return '{'.\implode(',', $processed).'}';
    }

    /**
     * Formats a single value for PostgreSQL array.
     */
    private static function formatValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (\is_int($value) || \is_float($value)) {
            return (string) $value;
        }

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

        if ($stringValue === '') {
            return '""';
        }

        // Make sure strings are quoted, PostgreSQL will handle this gracefully
        // Double the backslashes and escape quotes
        $escaped = \str_replace(
            ['\\', '"'],
            ['\\\\', '\"'],
            $stringValue
        );

        return '"'.$escaped.'"';
    }
}
