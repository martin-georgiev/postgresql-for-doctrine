<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPathForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPathForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidPathException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Path as PathValueObject;

/**
 * Implementation of PostgreSQL PATH data type.
 *
 * @see https://www.postgresql.org/docs/current/datatype-geometric.html#id-1.5.7.16.9
 *
 * @since 4.4
 */
class Path extends BaseType
{
    protected const TYPE_NAME = Type::PATH;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof PathValueObject) {
            throw InvalidPathForPHPException::forInvalidType($value);
        }

        return (string) $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?PathValueObject
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidPathForDatabaseException::forInvalidType($value);
        }

        try {
            return PathValueObject::fromString($value);
        } catch (InvalidPathException) {
            throw InvalidPathForDatabaseException::forInvalidFormat($value);
        }
    }
}
