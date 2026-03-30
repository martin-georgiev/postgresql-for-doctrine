<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPolygonArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPolygonArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Polygon as PolygonValueObject;

/**
 * Implementation of PostgreSQL POLYGON[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-POLYGON
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class PolygonArray extends BaseGeometricArray
{
    protected const TYPE_NAME = Type::POLYGON_ARRAY;

    protected function getValueObjectClass(): string
    {
        return PolygonValueObject::class;
    }

    protected function createValueObjectFromString(string $value): PolygonValueObject
    {
        return PolygonValueObject::fromString($value);
    }

    protected function throwTypedInvalidArrayTypeException(mixed $value): never
    {
        throw InvalidPolygonArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidPolygonArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never
    {
        throw InvalidPolygonArrayItemForDatabaseException::forInvalidType($item);
    }
}
