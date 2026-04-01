<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPathArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPathArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Path as PathValueObject;

/**
 * Implementation of PostgreSQL PATH[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-GEOMETRIC-PATHS
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class PathArray extends BaseGeometricArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::PATH_ARRAY;

    protected function getValueObjectClass(): string
    {
        return PathValueObject::class;
    }

    protected function createValueObjectFromString(string $value): PathValueObject
    {
        return PathValueObject::fromString($value);
    }

    protected function throwTypedInvalidArrayTypeException(mixed $value): never
    {
        throw InvalidPathArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidPathArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never
    {
        throw InvalidPathArrayItemForDatabaseException::forInvalidType($item);
    }
}
