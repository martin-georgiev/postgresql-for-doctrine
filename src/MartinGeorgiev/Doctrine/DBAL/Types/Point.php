<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPointForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPointForPHPException;
use MartinGeorgiev\ValueObject\Point as PointValueObject;

/**
 * Implementation of PostgreSQL POINT data type.
 *
 * @see https://www.postgresql.org/docs/current/datatype-geometric.html#DATATYPE-GEOMETRIC-POINTS
 * @since 3.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
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

        if (!\preg_match('/\((-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)\)/', $value, $matches)) {
            throw InvalidPointForDatabaseException::forInvalidFormat($value);
        }

        return new PointValueObject((float) $matches[1], (float) $matches[2]);
    }
}
