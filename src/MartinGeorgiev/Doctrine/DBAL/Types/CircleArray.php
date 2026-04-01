<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCircleArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCircleArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Circle as CircleValueObject;

/**
 * Implementation of PostgreSQL CIRCLE[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-CIRCLE
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class CircleArray extends BaseGeometricArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::CIRCLE_ARRAY;

    protected function getValueObjectClass(): string
    {
        return CircleValueObject::class;
    }

    protected function createValueObjectFromString(string $value): CircleValueObject
    {
        return CircleValueObject::fromString($value);
    }

    protected function throwTypedInvalidArrayTypeException(mixed $value): never
    {
        throw InvalidCircleArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidCircleArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never
    {
        throw InvalidCircleArrayItemForDatabaseException::forInvalidType($item);
    }
}
