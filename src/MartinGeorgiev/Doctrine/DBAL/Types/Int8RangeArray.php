<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInt8RangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInt8RangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range as Int8RangeValueObject;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Implementation of PostgreSQL INT8RANGE[] data type.
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Int8RangeArray extends BaseArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::INT8RANGE_ARRAY;

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        if (!$item instanceof Int8RangeValueObject) {
            throw InvalidInt8RangeArrayItemForDatabaseException::forInvalidType($item);
        }

        return $this->quoteAndEscapeArrayItem((string) $item);
    }

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return $item === null || $item instanceof Int8RangeValueObject;
    }

    public function transformArrayItemForPHP(mixed $item): ?Int8RangeValueObject
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw InvalidInt8RangeArrayItemForPHPException::forInvalidType($item);
        }

        try {
            return Int8RangeValueObject::fromString($item);
        } catch (\InvalidArgumentException) {
            throw InvalidInt8RangeArrayItemForPHPException::forInvalidFormat($item);
        }
    }

    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($postgresArray);
    }

    protected function throwInvalidTypeException(mixed $value): never
    {
        throw InvalidInt8RangeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwInvalidItemException(mixed $item): never
    {
        throw InvalidInt8RangeArrayItemForDatabaseException::forInvalidType($item);
    }
}
