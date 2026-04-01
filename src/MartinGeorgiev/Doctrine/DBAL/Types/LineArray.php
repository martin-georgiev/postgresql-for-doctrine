<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLineArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLineArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Line as LineValueObject;

/**
 * Implementation of PostgreSQL LINE[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-LINE
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class LineArray extends BaseGeometricArray
{
    protected const TYPE_NAME = Type::LINE_ARRAY;

    protected function getValueObjectClass(): string
    {
        return LineValueObject::class;
    }

    protected function createValueObjectFromString(string $value): LineValueObject
    {
        return LineValueObject::fromString($value);
    }

    protected function throwTypedInvalidArrayTypeException(mixed $value): never
    {
        throw InvalidLineArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidLineArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never
    {
        throw InvalidLineArrayItemForDatabaseException::forInvalidType($item);
    }
}
