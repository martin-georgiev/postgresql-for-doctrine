<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInt4MultirangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInt4MultirangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Multirange as Int4MultirangeValueObject;

/**
 * Implementation of PostgreSQL INT4MULTIRANGE[] data type.
 *
 * @extends BaseMultirangeArray<Int4MultirangeValueObject>
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Int4MultirangeArray extends BaseMultirangeArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::INT4MULTIRANGE_ARRAY;

    protected function getValueObjectClass(): string
    {
        return Int4MultirangeValueObject::class;
    }

    protected function createValueObjectFromString(string $value): Int4MultirangeValueObject
    {
        return Int4MultirangeValueObject::fromString($value);
    }

    protected function throwTypedInvalidTypeExceptionForPHP(mixed $item): never
    {
        throw InvalidInt4MultirangeArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwTypedInvalidArrayTypeException(mixed $value): never
    {
        throw InvalidInt4MultirangeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidInt4MultirangeArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never
    {
        throw InvalidInt4MultirangeArrayItemForDatabaseException::forInvalidType($item);
    }
}
