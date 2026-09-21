<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidNumericArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidNumericArrayItemForPHPException;

/**
 * Implementation of PostgreSQL NUMERIC[] data type.
 *
 * Array items are handled as numeric strings so the exact precision and scale of the values survive the round-trip
 * (e.g. trailing zeros like "502.00"). PHP integers and floats are rejected as array items - floats cannot represent
 * arbitrary-precision decimals without data loss.
 * PostgreSQL treats DECIMAL[] as an alias of NUMERIC[], so this type covers both.
 * NUMERIC carries the non-finite values "NaN", "Infinity" and "-Infinity", which are items like any other here.
 * PostgreSQL has stored NaN since forever, while Infinity arrived with PostgreSQL 14 and earlier servers reject it.
 *
 * @see https://www.postgresql.org/docs/18/datatype-numeric.html#DATATYPE-NUMERIC-DECIMAL
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class NumericArray extends BaseStringArray
{
    /**
     * Scientific notation, a leading plus sign and a bare decimal point (".5") are accepted by PostgreSQL on input.
     * They never appear in its output. The non-finite values are matched in the single spelling PostgreSQL prints.
     *
     * @var string
     */
    private const NUMERIC_REGEX = '/^(?:-?\d+(\.\d+)?|NaN|-?Infinity)\z/';

    /**
     * @var string
     */
    protected const TYPE_NAME = Type::NUMERIC_ARRAY;

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        if ($item === null) {
            return true;
        }

        if (!\is_string($item)) {
            return false;
        }

        return \preg_match(self::NUMERIC_REGEX, $item) === 1;
    }

    protected function throwInvalidTypeExceptionForPHP(mixed $item): never
    {
        throw InvalidNumericArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidNumericArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidNumericArrayItemForDatabaseException::forInvalidFormat($item);
    }
}
