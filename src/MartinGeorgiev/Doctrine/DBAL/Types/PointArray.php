<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPointArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPointArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point as PointValueObject;

/**
 * Implementation of PostgreSQL POINT[] data type.
 *
 * @see https://www.postgresql.org/docs/17/datatype-geometric.html#DATATYPE-GEOMETRIC-POINTS
 * @since 3.1
 *
 * @author Sébastien Jean <sebastien.jean76@gmail.com>
 */
class PointArray extends BaseGeometricArray
{
    protected const TYPE_NAME = Type::POINT_ARRAY;

    protected function getValueObjectClass(): string
    {
        return PointValueObject::class;
    }

    protected function createValueObjectFromString(string $value): PointValueObject
    {
        return PointValueObject::fromString($value);
    }

    protected function throwTypedInvalidArrayTypeException(mixed $value): never
    {
        throw InvalidPointArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidPointArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never
    {
        throw InvalidPointArrayItemForDatabaseException::forInvalidType($item);
    }
}
