<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Base class for PostGIS spatial types (GEOMETRY, GEOGRAPHY).
 *
 * Provides common functionality for spatial types that need to convert
 * between binary (EWKB) and text (EWKT) formats.
 *
 * @since 3.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseSpatialType extends BaseType
{
    /**
     * Modifies the SQL expression to convert spatial data from binary to EWKT format.
     *
     * This ensures PostgreSQL returns spatial data in text format (EWKT) instead of binary (EWKB).
     * This method is called by Doctrine ORM when generating SELECT queries.
     *
     * The SQL expression handles SRID:
     * - If SRID is 0 (no SRID), returns plain WKT: "POINT(1 2)"
     * - If SRID is set, returns EWKT with SRID prefix: "SRID=4326;POINT(1 2)"
     *
     * @param non-empty-string $sqlExpr
     * @param AbstractPlatform $platform
     */
    public function convertToPHPValueSQL($sqlExpr, $platform): string
    {
        return \sprintf(
            "CASE WHEN ST_SRID(%s) = 0 THEN ST_AsText(%s) ELSE 'SRID=' || ST_SRID(%s) || ';' || ST_AsText(%s) END",
            $sqlExpr,
            $sqlExpr,
            $sqlExpr,
            $sqlExpr
        );
    }
}
