<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTstzRangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTstzRangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzRange as TstzRangeValueObject;

/**
 * Implementation of PostgreSQL TSTZRANGE[] data type.
 *
 * @extends BaseRangeArray<TstzRangeValueObject>
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class TstzRangeArray extends BaseRangeArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::TSTZRANGE_ARRAY;

    protected function getValueObjectClass(): string
    {
        return TstzRangeValueObject::class;
    }

    protected function createValueObjectFromString(string $value): TstzRangeValueObject
    {
        return TstzRangeValueObject::fromString($value);
    }

    protected function throwTypedInvalidTypeExceptionForPHP(mixed $item): never
    {
        throw InvalidTstzRangeArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwTypedInvalidArrayTypeException(mixed $value): never
    {
        throw InvalidTstzRangeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidTstzRangeArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never
    {
        throw InvalidTstzRangeArrayItemForDatabaseException::forInvalidType($item);
    }
}
