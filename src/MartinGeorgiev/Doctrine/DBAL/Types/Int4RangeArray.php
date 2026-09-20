<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInt4RangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidInt4RangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Range as Int4RangeValueObject;

/**
 * Implementation of PostgreSQL INT4RANGE[] data type.
 *
 * @extends BaseRangeArray<Int4RangeValueObject>
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Int4RangeArray extends BaseRangeArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::INT4RANGE_ARRAY;

    protected function getValueObjectClass(): string
    {
        return Int4RangeValueObject::class;
    }

    protected function createValueObjectFromString(string $value): Int4RangeValueObject
    {
        return Int4RangeValueObject::fromString($value);
    }

    protected function createInvalidTypeExceptionForPHP(mixed $item): ConversionException
    {
        return InvalidInt4RangeArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwTypedInvalidArrayTypeException(mixed $value): never
    {
        throw InvalidInt4RangeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidInt4RangeArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never
    {
        throw InvalidInt4RangeArrayItemForDatabaseException::forInvalidType($item);
    }
}
