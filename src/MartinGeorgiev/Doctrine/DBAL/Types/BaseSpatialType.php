<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\SpatialColumnOptionsSQLDeclarationTrait;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\SpatialDataReadTrait;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

/**
 * Base class for PostGIS spatial types (GEOMETRY, GEOGRAPHY).
 *
 * Provides common functionality for spatial types that need to convert between binary (EWKB) and text (EWKT) formats.
 *
 * @since 3.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseSpatialType extends BaseType
{
    use SpatialColumnOptionsSQLDeclarationTrait;
    use SpatialDataReadTrait;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof WktSpatialData) {
            $this->throwInvalidTypeExceptionForDatabase($value);
        }

        return (string) $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?WktSpatialData
    {
        return $this->readWktSpatialData($value);
    }

    /**
     * @param non-empty-string $sqlExpr
     * @param AbstractPlatform $platform
     */
    public function convertToPHPValueSQL($sqlExpr, $platform): string
    {
        return $this->selectAsEwkt($sqlExpr);
    }

    abstract protected function throwInvalidTypeExceptionForDatabase(mixed $value): never;
}
