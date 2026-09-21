<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsRangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTsRangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsRange as TsRangeValueObject;

/**
 * Implementation of PostgreSQL TSRANGE[] data type.
 *
 * @extends BaseRangeArray<TsRangeValueObject>
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class TsRangeArray extends BaseRangeArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::TSRANGE_ARRAY;

    protected function getValueObjectClass(): string
    {
        return TsRangeValueObject::class;
    }

    protected function createValueObjectFromString(string $value): TsRangeValueObject
    {
        return TsRangeValueObject::fromString($value);
    }

    protected function throwTypedInvalidTypeExceptionForPHP(mixed $item): never
    {
        throw InvalidTsRangeArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwTypedInvalidArrayTypeException(mixed $value): never
    {
        throw InvalidTsRangeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidTsRangeArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never
    {
        throw InvalidTsRangeArrayItemForDatabaseException::forInvalidType($item);
    }
}
