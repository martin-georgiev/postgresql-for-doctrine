<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLsegArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLsegArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Lseg as LsegValueObject;

/**
 * Implementation of PostgreSQL LSEG[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-LSEG
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class LsegArray extends BaseGeometricArray
{
    protected const TYPE_NAME = Type::LSEG_ARRAY;

    protected function getValueObjectClass(): string
    {
        return LsegValueObject::class;
    }

    protected function createValueObjectFromString(string $value): LsegValueObject
    {
        return LsegValueObject::fromString($value);
    }

    protected function throwTypedInvalidArrayTypeException(mixed $value): never
    {
        throw InvalidLsegArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidLsegArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never
    {
        throw InvalidLsegArrayItemForDatabaseException::forInvalidType($item);
    }
}
