<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBoxArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBoxArrayItemForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Box as BoxValueObject;

/**
 * Implementation of PostgreSQL BOX[] data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-GEOMETRIC-BOXES
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class BoxArray extends BaseGeometricArray
{
    protected const TYPE_NAME = Type::BOX_ARRAY;

    protected function getValueObjectClass(): string
    {
        return BoxValueObject::class;
    }

    protected function createValueObjectFromString(string $value): BoxValueObject
    {
        return BoxValueObject::fromString($value);
    }

    protected function throwTypedInvalidArrayTypeException(mixed $value): never
    {
        throw InvalidBoxArrayItemForPHPException::forInvalidArrayType($value);
    }

    protected function throwTypedInvalidFormatExceptionForPHP(mixed $value): never
    {
        throw InvalidBoxArrayItemForPHPException::forInvalidFormat($value);
    }

    protected function throwTypedInvalidItemExceptionForDatabase(mixed $item): never
    {
        throw InvalidBoxArrayItemForDatabaseException::forInvalidType($item);
    }
}
