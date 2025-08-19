<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPointForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPointForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidPointException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point as PointValueObject;

/**
 * Implementation of PostgreSQL POINT data type.
 *
 * @see https://www.postgresql.org/docs/17/datatype-geometric.html#DATATYPE-GEOMETRIC-POINTS
 * @since 3.1
 *
 * @author Sébastien Jean <sebastien.jean76@gmail.com>
 */
class Point extends BaseType
{
    protected const TYPE_NAME = 'point';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof PointValueObject) {
            throw InvalidPointForPHPException::forInvalidType($value);
        }

        return (string) $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?PointValueObject
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidPointForDatabaseException::forInvalidType($value);
        }

        try {
            return PointValueObject::fromString($value);
        } catch (InvalidPointException) {
            throw InvalidPointForDatabaseException::forInvalidFormat($value);
        }
    }
}
