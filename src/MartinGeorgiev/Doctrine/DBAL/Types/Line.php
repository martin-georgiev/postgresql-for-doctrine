<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLineForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLineForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidLineException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Line as LineValueObject;

/**
 * Implementation of PostgreSQL LINE data type.
 *
 * @see https://www.postgresql.org/docs/current/datatype-geometric.html#DATATYPE-LINE
 *
 * @since 4.4
 */
class Line extends BaseType
{
    protected const TYPE_NAME = Type::LINE;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof LineValueObject) {
            throw InvalidLineForPHPException::forInvalidType($value);
        }

        return (string) $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?LineValueObject
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidLineForDatabaseException::forInvalidType($value);
        }

        try {
            return LineValueObject::fromString($value);
        } catch (InvalidLineException) {
            throw InvalidLineForDatabaseException::forInvalidFormat($value);
        }
    }
}
