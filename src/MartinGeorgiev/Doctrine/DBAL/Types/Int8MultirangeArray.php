<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInt8MultirangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInt8MultirangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Multirange as Int8MultirangeValueObject;

/**
 * Implementation of PostgreSQL INT8MULTIRANGE[] data type.
 *
 * @extends BaseMultirangeArray<Int8MultirangeValueObject>
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Int8MultirangeArray extends BaseMultirangeArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::INT8MULTIRANGE_ARRAY;

    protected function getValueObjectClass(): string
    {
        return Int8MultirangeValueObject::class;
    }

    protected function createValueObjectFromString(string $value): Int8MultirangeValueObject
    {
        return Int8MultirangeValueObject::fromString($value);
    }

    protected function throwTypedInvalidTypeExceptionForPHP(mixed $item): never
    {
        throw InvalidInt8MultirangeArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwTypedInvalidArrayTypeException(mixed $value): never
    {
        throw InvalidInt8MultirangeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidInt8MultirangeArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never
    {
        throw InvalidInt8MultirangeArrayItemForDatabaseException::forInvalidType($item);
    }
}
