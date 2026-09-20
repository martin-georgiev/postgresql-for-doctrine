<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInt8RangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInt8RangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range as Int8RangeValueObject;

/**
 * Implementation of PostgreSQL INT8RANGE[] data type.
 *
 * @extends BaseRangeArray<Int8RangeValueObject>
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Int8RangeArray extends BaseRangeArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::INT8RANGE_ARRAY;

    protected function getValueObjectClass(): string
    {
        return Int8RangeValueObject::class;
    }

    protected function createValueObjectFromString(string $value): Int8RangeValueObject
    {
        return Int8RangeValueObject::fromString($value);
    }

    protected function createInvalidTypeExceptionForPHP(mixed $item): ConversionException
    {
        return InvalidInt8RangeArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwTypedInvalidArrayTypeException(mixed $value): never
    {
        throw InvalidInt8RangeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidInt8RangeArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never
    {
        throw InvalidInt8RangeArrayItemForDatabaseException::forInvalidType($item);
    }
}
