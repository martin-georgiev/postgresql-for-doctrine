<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateRangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateRangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange as DateRangeValueObject;

/**
 * Implementation of PostgreSQL DATERANGE[] data type.
 *
 * @extends BaseRangeArray<DateRangeValueObject>
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class DateRangeArray extends BaseRangeArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::DATERANGE_ARRAY;

    protected function getValueObjectClass(): string
    {
        return DateRangeValueObject::class;
    }

    protected function createValueObjectFromString(string $value): DateRangeValueObject
    {
        return DateRangeValueObject::fromString($value);
    }

    protected function createInvalidTypeExceptionForPHP(mixed $item): ConversionException
    {
        return InvalidDateRangeArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwTypedInvalidArrayTypeException(mixed $value): never
    {
        throw InvalidDateRangeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidDateRangeArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never
    {
        throw InvalidDateRangeArrayItemForDatabaseException::forInvalidType($item);
    }
}
