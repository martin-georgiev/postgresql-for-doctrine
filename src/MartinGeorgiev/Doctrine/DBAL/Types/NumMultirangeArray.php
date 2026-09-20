<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidNumMultirangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidNumMultirangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericMultirange as NumericMultirangeValueObject;

/**
 * Implementation of PostgreSQL NUMMULTIRANGE[] data type.
 *
 * @extends BaseMultirangeArray<NumericMultirangeValueObject>
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class NumMultirangeArray extends BaseMultirangeArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::NUMMULTIRANGE_ARRAY;

    protected function getValueObjectClass(): string
    {
        return NumericMultirangeValueObject::class;
    }

    protected function createValueObjectFromString(string $value): NumericMultirangeValueObject
    {
        return NumericMultirangeValueObject::fromString($value);
    }

    protected function createInvalidTypeExceptionForPHP(mixed $item): ConversionException
    {
        return InvalidNumMultirangeArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwTypedInvalidArrayTypeException(mixed $value): never
    {
        throw InvalidNumMultirangeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidNumMultirangeArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never
    {
        throw InvalidNumMultirangeArrayItemForDatabaseException::forInvalidType($item);
    }
}
