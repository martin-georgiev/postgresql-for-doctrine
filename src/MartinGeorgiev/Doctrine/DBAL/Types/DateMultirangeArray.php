<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateMultirangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidDateMultirangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateMultirange as DateMultirangeValueObject;

/**
 * Implementation of PostgreSQL DATEMULTIRANGE[] data type.
 *
 * @extends BaseMultirangeArray<DateMultirangeValueObject>
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class DateMultirangeArray extends BaseMultirangeArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::DATEMULTIRANGE_ARRAY;

    protected function getValueObjectClass(): string
    {
        return DateMultirangeValueObject::class;
    }

    protected function createValueObjectFromString(string $value): DateMultirangeValueObject
    {
        return DateMultirangeValueObject::fromString($value);
    }

    protected function throwTypedInvalidTypeExceptionForPHP(mixed $item): never
    {
        throw InvalidDateMultirangeArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwTypedInvalidArrayTypeException(mixed $value): never
    {
        throw InvalidDateMultirangeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidDateMultirangeArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never
    {
        throw InvalidDateMultirangeArrayItemForDatabaseException::forInvalidType($item);
    }
}
