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
final class WktSpatialData implements \Stringable
{
    private function __construct(
        private readonly ?int $srid,
        private readonly GeometryType $geometryType,
        private readonly string $wktBody,
        private readonly ?DimensionalModifier $dimensionalModifier = null
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
