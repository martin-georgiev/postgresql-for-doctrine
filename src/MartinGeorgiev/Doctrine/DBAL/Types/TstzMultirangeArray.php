<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTstzMultirangeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidTstzMultirangeArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzMultirange as TstzMultirangeValueObject;

/**
 * Implementation of PostgreSQL TSTZMULTIRANGE[] data type.
 *
 * @extends BaseMultirangeArray<TstzMultirangeValueObject>
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class TstzMultirangeArray extends BaseMultirangeArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::TSTZMULTIRANGE_ARRAY;

    protected function getValueObjectClass(): string
    {
        return TstzMultirangeValueObject::class;
    }

    protected function createValueObjectFromString(string $value): TstzMultirangeValueObject
    {
        return TstzMultirangeValueObject::fromString($value);
    }

    protected function throwTypedInvalidTypeExceptionForPHP(mixed $item): never
    {
        throw InvalidTstzMultirangeArrayItemForPHPException::forInvalidType($item);
    }

    protected function throwTypedInvalidArrayTypeException(mixed $value): never
    {
        throw InvalidTstzMultirangeArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidTstzMultirangeArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never
    {
        throw InvalidTstzMultirangeArrayItemForDatabaseException::forInvalidType($item);
    }
}
