<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidWktSpatialDataException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

/**
 * Reads a PostGIS value as EWKT text and turns it into a WktSpatialData, for the scalar and the array spatial types alike.
 *
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
trait SpatialDataReadTrait
{
    /**
     * ORM 2.x applies convertToPHPValueSQL() only to types that declare they can require SQL conversion.
     */
    public function canRequireSQLConversion(): bool
    {
        return true;
    }

    /**
     * PostgreSQL otherwise returns a spatial value as EWKB hex, which WktSpatialData cannot parse.
     * ST_AsText drops the SRID, so a non-zero one is put back in front as the EWKT prefix: "SRID=4326;POINT(1 2)".
     * ST_AsText keeps 15 decimals by default and drops the rest; 25 is enough for every double to read back unchanged.
     */
    private function selectAsEwkt(string $spatialSqlExpr): string
    {
        return \sprintf(
            "CASE WHEN ST_SRID(%1\$s) = 0 THEN ST_AsText(%1\$s, 25) ELSE 'SRID=' || ST_SRID(%1\$s) || ';' || ST_AsText(%1\$s, 25) END",
            $spatialSqlExpr
        );
    }

    private function readWktSpatialData(mixed $value): ?WktSpatialData
    {
        if ($value === null) {
            return null;
        }

        if (!\is_string($value)) {
            $this->throwInvalidTypeExceptionForPHP($value);
        }

        try {
            return WktSpatialData::fromString($value);
        } catch (InvalidWktSpatialDataException) {
            $this->throwInvalidFormatExceptionForPHP($value);
        }
    }

    abstract protected function throwInvalidTypeExceptionForPHP(mixed $value): never;

    abstract protected function throwInvalidFormatExceptionForPHP(mixed $value): never;
}
