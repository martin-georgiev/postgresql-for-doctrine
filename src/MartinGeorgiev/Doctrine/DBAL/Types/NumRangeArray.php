<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidNumRangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidNumRangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange as NumericRangeValueObject;

/**
 * Implementation of PostgreSQL NUMRANGE[] data type.
 *
 * @extends BaseRangeArray<NumericRangeValueObject>
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class NumRangeArray extends BaseRangeArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::NUMRANGE_ARRAY;

    protected function getValueObjectClass(): string
    {
        return NumericRangeValueObject::class;
    }

    protected function createValueObjectFromString(string $value): NumericRangeValueObject
    {
        return NumericRangeValueObject::fromString($value);
    }

    protected function createInvalidTypeExceptionForPHP(mixed $item): ConversionException
    {
        return InvalidNumRangeArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwTypedInvalidArrayTypeException(mixed $value): never
    {
        throw InvalidNumRangeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidNumRangeArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never
    {
        throw InvalidNumRangeArrayItemForDatabaseException::forInvalidType($item);
    }
}
