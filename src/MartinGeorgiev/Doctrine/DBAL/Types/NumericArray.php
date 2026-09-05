<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidNumericArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidNumericArrayItemForPHPException;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL NUMERIC[] data type.
 *
 * Array items are handled as numeric strings so the exact precision and scale
 * of the values survive the round-trip (e.g. trailing zeros like "502.00").
 * PHP integers and floats are rejected as array items - floats cannot represent
 * arbitrary-precision decimals without data loss. PostgreSQL treats DECIMAL[]
 * as an alias of NUMERIC[], so this type covers both.
 *
 * @see https://www.postgresql.org/docs/18/datatype-numeric.html#DATATYPE-NUMERIC-DECIMAL
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class NumericArray extends BaseStringArray
{
    /**
     * Scientific notation, a leading plus sign and a bare decimal point (".5") are
     * accepted by PostgreSQL on input but never appear in its output, so allowing
     * them would break string round-trips.
     *
     * @var string
     */
    private const NUMERIC_REGEX = '/^-?\d+(\.\d+)?$/';

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

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        // PostgreSQL returns numeric array items unquoted; type inference would
        // convert them to floats and lose exact precision (see GitHub issue #482).
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray(
            $postgresArray,
            preserveStringTypes: true
        );
    }

    public function transformArrayItemForPHP(mixed $item): ?string
    {
        // String-preserving parsing skips NULL token inference; mapping it here is
        // unambiguous because "NULL" can never appear as a value of a numeric column.
        if ($item === 'NULL') {
            return null;
        }

        return parent::transformArrayItemForPHP($item);
    }

    protected function createInvalidTypeExceptionForPHP(mixed $item): InvalidNumericArrayItemForPHPException
    {
        return InvalidNumericArrayItemForPHPException::forInvalidType($item);
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
