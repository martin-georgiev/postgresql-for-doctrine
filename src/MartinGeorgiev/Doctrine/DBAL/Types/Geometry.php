<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeometryForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeometryForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidWktSpatialDataException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

/**
 * Implementation of PostGIS GEOMETRY data type.
 *
 * @see https://postgis.net/docs/using_postgis_dbmanagement.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Geometry extends BaseType
{
    protected const TYPE_NAME = 'geometry';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof WktSpatialData) {
            throw InvalidGeometryForPHPException::forInvalidType($value);
        }

        return (string) $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?WktSpatialData
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidGeometryForDatabaseException::forInvalidType($value);
        }

        try {
            return WktSpatialData::fromWkt($value);
        } catch (InvalidWktSpatialDataException) {
            throw InvalidGeometryForDatabaseException::forInvalidFormat($value);
        }
    }
}
