<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitVaryingArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBitVaryingArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\BitValidationTrait;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL BIT VARYING[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-bit.html
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class BitVaryingArray extends BaseStringArray
{
    use BitValidationTrait;

    /**
     * @var string
     */
    protected const TYPE_NAME = Type::BIT_VARYING_ARRAY;

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray, true);
    }

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        if ($item === null) {
            return true;
        }

        if (!\is_string($item)) {
            return false;
        }

        return $this->isValidBitString($item);
    }

    public function transformArrayItemForPHP(mixed $item): ?string
    {
        if ($item === null || $item === 'NULL') {
            return null;
        }

        $result = parent::transformArrayItemForPHP($item);

        if ($result !== null && !$this->isValidBitString($result)) {
            throw InvalidBitVaryingArrayItemForPHPException::forInvalidFormat($item);
        }

        return $result;
    }

    protected function createInvalidTypeExceptionForPHP(mixed $item): InvalidBitVaryingArrayItemForPHPException
    {
        return InvalidBitVaryingArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidBitVaryingArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidBitVaryingArrayItemForDatabaseException::forInvalidFormat($item);
    }
}
