<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCircleForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCircleForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Circle as CircleValueObject;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidCircleException;

/**
 * Implementation of PostgreSQL CIRCLE data type.
 *
 * @see https://www.postgresql.org/docs/current/datatype-geometric.html#DATATYPE-CIRCLE
 *
 * @since 4.4
 */
class Circle extends BaseType
{
    protected const TYPE_NAME = Type::CIRCLE;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof CircleValueObject) {
            throw InvalidCircleForPHPException::forInvalidType($value);
        }

        return (string) $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?CircleValueObject
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidCircleForDatabaseException::forInvalidType($value);
        }

        try {
            return CircleValueObject::fromString($value);
        } catch (InvalidCircleException) {
            throw InvalidCircleForDatabaseException::forInvalidFormat($value);
        }
    }
}
