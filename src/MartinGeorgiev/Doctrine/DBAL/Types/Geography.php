<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeographyForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeographyForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidWktSpatialDataException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

/**
 * Implementation of PostGIS GEOGRAPHY data type.
 *
 * @see https://postgis.net/docs/using_postgis_dbmanagement.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Geography extends BaseSpatialType
{
    protected const TYPE_NAME = Type::GEOGRAPHY;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof WktSpatialData) {
            throw InvalidGeographyForPHPException::forInvalidType($value);
        }

        return (string) $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?WktSpatialData
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidGeographyForDatabaseException::forInvalidType($value);
        }

        try {
            return WktSpatialData::fromWkt($value);
        } catch (InvalidWktSpatialDataException) {
            throw InvalidGeographyForDatabaseException::forInvalidFormat($value);
        }
    }
}
