<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidSpatialColumnDeclarationException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DimensionalModifier;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\GeometryType;

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
     * PostGIS accepts this as the "any geometry" subtype, which is what a column
     * constrained only by SRID needs.
     *
     * @var string
     */
    private const ANY_GEOMETRY_SUBTYPE = 'GEOMETRY';

    /**
     * Emits the PostGIS type modifier when the `geometry_type` and/or `srid` column
     * options are declared, so the subtype and spatial reference system are enforced
     * by the database instead of only by the application.
     *
     * Without either option the declaration is left untouched for backwards compatibility.
     *
     * @param array<string, mixed> $fieldDeclaration
     *
     * @throws InvalidSpatialColumnDeclarationException
     *
     * @since 4.8
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        $geometryType = $this->readGeometryTypeOption($fieldDeclaration);
        $srid = $this->readSridOption($fieldDeclaration);

        if ($geometryType === null && $srid === null) {
            return parent::getSQLDeclaration($fieldDeclaration, $platform);
        }

        $typeName = \strtoupper(static::TYPE_NAME);
        $subtype = $geometryType ?? self::ANY_GEOMETRY_SUBTYPE;

        if ($srid === null) {
            return \sprintf('%s(%s)', $typeName, $subtype);
        }

        return \sprintf('%s(%s,%d)', $typeName, $subtype, $srid);
    }

    /**
     * @param array<string, mixed> $fieldDeclaration
     *
     * @throws InvalidSpatialColumnDeclarationException
     */
    private function readGeometryTypeOption(array $fieldDeclaration): ?string
    {
        $geometryType = $fieldDeclaration['geometry_type'] ?? null;
        if ($geometryType === null) {
            return null;
        }

        if (!\is_string($geometryType)) {
            throw InvalidSpatialColumnDeclarationException::forInvalidGeometryType($geometryType);
        }

        $normalised = \strtoupper(\trim($geometryType));
        if (!\in_array($normalised, $this->buildSupportedSubtypes(), true)) {
            throw InvalidSpatialColumnDeclarationException::forInvalidGeometryType($geometryType);
        }

        return $normalised;
    }

    /**
     * @return list<string>
     */
    private function buildSupportedSubtypes(): array
    {
        $subtypes = [self::ANY_GEOMETRY_SUBTYPE];
        foreach (GeometryType::cases() as $geometryType) {
            $subtypes[] = $geometryType->value;
            foreach (DimensionalModifier::cases() as $dimensionalModifier) {
                $subtypes[] = $geometryType->value.$dimensionalModifier->value;
            }
        }

        return $subtypes;
    }

    /**
     * @param array<string, mixed> $fieldDeclaration
     *
     * @throws InvalidSpatialColumnDeclarationException
     */
    private function readSridOption(array $fieldDeclaration): ?int
    {
        $srid = $fieldDeclaration['srid'] ?? null;
        if ($srid === null) {
            return null;
        }

        // XML and YAML mappings deliver column options as strings, so digit strings are accepted too.
        if (\is_string($srid) && \ctype_digit($srid)) {
            return (int) $srid;
        }

        if (\is_int($srid) && $srid >= 0) {
            return $srid;
        }

        throw InvalidSpatialColumnDeclarationException::forInvalidSrid($srid);
    }

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
