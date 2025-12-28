<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidWktSpatialDataException;

/**
 * Spatial data value object supporting EWKT (with optional SRID prefix) and WKT.
 *
 * Examples:
 * - POINT(1 2)
 * - SRID=4326;POINT(-122.4194 37.7749)
 * - LINESTRING(0 0, 1 1)
 * - POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))
 *
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final readonly class WktSpatialData implements \Stringable
{
    private function __construct(
        private ?int $srid,
        private GeometryType $geometryType,
        private string $wktBody,
        private ?DimensionalModifier $dimensionalModifier = null
    ) {}

    public function __toString(): string
    {
        $typeWithModifier = $this->geometryType->value;
        if ($this->dimensionalModifier instanceof DimensionalModifier) {
            $typeWithModifier .= ' '.$this->dimensionalModifier->value;
        }

        $typeAndBody = $typeWithModifier.'('.$this->wktBody.')';
        if ($this->srid === null) {
            return $typeAndBody;
        }

        return 'SRID='.$this->srid.';'.$typeAndBody;
    }

    public static function fromWkt(string $wkt): self
    {
        $wkt = \trim($wkt);
        if ($wkt === '') {
            throw InvalidWktSpatialDataException::forEmptyWkt();
        }

        $srid = null;
        $expectSrid = \str_starts_with($wkt, 'SRID=');
        if ($expectSrid) {
            $sridSeparatorPosition = \strpos($wkt, ';');
            if ($sridSeparatorPosition === false) {
                throw InvalidWktSpatialDataException::forMissingSemicolonInEwkt();
            }

            $sridRawValue = \substr($wkt, 5, $sridSeparatorPosition - 5);
            if ($sridRawValue === '' || !\ctype_digit($sridRawValue)) {
                throw InvalidWktSpatialDataException::forInvalidSridValue($sridRawValue);
            }

            $srid = (int) $sridRawValue;
            $wkt = \substr($wkt, $sridSeparatorPosition + 1);
        }

        $wktTypeWithOptionalModifiersPattern = '/^([A-Z][A-Z0-9_]*)(?:\s+(ZM|Z|M))?\s*\((.*)\)$/s';
        if (!\preg_match($wktTypeWithOptionalModifiersPattern, $wkt, $matches)) {
            throw InvalidWktSpatialDataException::forInvalidWktFormat($wkt);
        }

        $typeString = $matches[1];
        $dimensionalModifier = empty($matches[2]) ? null : DimensionalModifier::tryFrom($matches[2]);
        $body = \trim($matches[3]);
        if ($body === '') {
            throw InvalidWktSpatialDataException::forEmptyCoordinateSection();
        }

        $geometryType = GeometryType::tryFrom($typeString);
        if ($geometryType === null) {
            throw InvalidWktSpatialDataException::forUnsupportedGeometryType($typeString);
        }

        return new self($srid, $geometryType, $body, $dimensionalModifier);
    }

    /**
     * Create spatial data from individual components.
     *
     * This is a convenience method for programmatically building spatial data
     * without manually constructing WKT strings.
     *
     * @param GeometryType $geometryType The geometry type (e.g., POINT, LINESTRING)
     * @param string $coordinates The coordinate data (e.g., "1 2" for a point, "0 0, 1 1" for a line)
     * @param int|null $srid Optional spatial reference system identifier
     * @param DimensionalModifier|null $dimensionalModifier Optional dimensional modifier (Z, M, or ZM)
     *
     * @throws InvalidWktSpatialDataException If coordinates are empty
     */
    public static function fromComponents(
        GeometryType $geometryType,
        string $coordinates,
        ?int $srid = null,
        ?DimensionalModifier $dimensionalModifier = null
    ): self {
        $coordinates = \trim($coordinates);
        if ($coordinates === '') {
            throw InvalidWktSpatialDataException::forEmptyCoordinateSection();
        }

        return new self($srid, $geometryType, $coordinates, $dimensionalModifier);
    }

    /**
     * Create a POINT geometry from longitude and latitude.
     *
     * Convenience method for the most common use case: creating geographic points.
     *
     * @param float|int|string $longitude The longitude (X coordinate)
     * @param float|int|string $latitude The latitude (Y coordinate)
     * @param int|null $srid Optional SRID (commonly 4326 for WGS84)
     */
    public static function point(
        float|int|string $longitude,
        float|int|string $latitude,
        ?int $srid = null
    ): self {
        return new self($srid, GeometryType::POINT, \sprintf('%s %s', $longitude, $latitude));
    }

    /**
     * Create a 3D POINT geometry with elevation.
     *
     * @param float|int|string $longitude The longitude (X coordinate)
     * @param float|int|string $latitude The latitude (Y coordinate)
     * @param float|int|string $elevation The elevation (Z coordinate)
     * @param int|null $srid Optional SRID (commonly 4326 for WGS84)
     */
    public static function point3d(
        float|int|string $longitude,
        float|int|string $latitude,
        float|int|string $elevation,
        ?int $srid = null
    ): self {
        return new self($srid, GeometryType::POINT, \sprintf('%s %s %s', $longitude, $latitude, $elevation), DimensionalModifier::Z);
    }

    public function getSrid(): ?int
    {
        return $this->srid;
    }

    public function getGeometryType(): GeometryType
    {
        return $this->geometryType;
    }

    public function getDimensionalModifier(): ?DimensionalModifier
    {
        return $this->dimensionalModifier;
    }

    public function getWkt(): string
    {
        return (string) $this;
    }
}
