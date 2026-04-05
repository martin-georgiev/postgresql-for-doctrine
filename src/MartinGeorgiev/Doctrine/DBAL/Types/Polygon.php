<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPolygonForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPolygonForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidPolygonException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Polygon as PolygonValueObject;

/**
 * Implementation of PostgreSQL POLYGON data type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-POLYGON
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Polygon extends BaseType
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::POLYGON;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof PolygonValueObject) {
            throw InvalidPolygonForPHPException::forInvalidType($value);
        }

        return (string) $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?PolygonValueObject
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            throw InvalidPolygonForDatabaseException::forInvalidType($value);
        }

        try {
            return PolygonValueObject::fromString($value);
        } catch (InvalidPolygonException) {
            throw InvalidPolygonForDatabaseException::forInvalidFormat($value);
        }
    }
}
