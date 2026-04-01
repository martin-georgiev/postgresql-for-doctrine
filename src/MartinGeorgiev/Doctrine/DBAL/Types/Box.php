<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBoxForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBoxForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Box as BoxValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidBoxException;

/**
 * Implementation of PostgreSQL BOX data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-GEOMETRIC-BOXES
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Box extends BaseType
{
    protected const TYPE_NAME = Type::BOX;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof BoxValueObject) {
            throw InvalidBoxForPHPException::forInvalidType($value);
        }

        return (string) $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?BoxValueObject
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidBoxForDatabaseException::forInvalidType($value);
        }

        try {
            return BoxValueObject::fromString($value);
        } catch (InvalidBoxException) {
            throw InvalidBoxForDatabaseException::forInvalidFormat($value);
        }
    }
}
