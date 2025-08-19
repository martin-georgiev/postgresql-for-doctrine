<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidGeometryException;

/**
 * Lightweight Geometry value object supporting Ewkt (with optional Srid prefix) and Wkt.
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
final class Geometry implements \Stringable
{
    private ?int $srid;

    private WktGeometryType $wktType;

    private string $wktBody;

    private function __construct(?int $srid, WktGeometryType $wktType, string $wktBody)
    {
        $this->srid = $srid;
        $this->wktType = $wktType;
        $this->wktBody = $wktBody;
    }

    public static function fromWkt(string $wkt): self
    {
        $wkt = trim($wkt);
        if ($wkt === '') {
            throw InvalidGeometryException::forEmptyWkt();
        }

        $srid = null;
        $expectSrid = str_starts_with($wkt, 'SRID=');
        if ($expectSrid) {
            $sridSeparatorPosition = strpos($wkt, ';');
            if ($sridSeparatorPosition === false) {
                throw InvalidGeometryException::forMissingSemicolonInEwkt();
            }
            $sridRawValue = substr($wkt, 5, $sridSeparatorPosition - 5);
            if ($sridRawValue === '' || !ctype_digit($sridRawValue)) {
                throw InvalidGeometryException::forInvalidSridValue($sridRawValue);
            }
            $srid = (int) $sridRawValue;
            $wkt = substr($wkt, $sridSeparatorPosition + 1);
        }

        $wktTypeWithOptionalModifiersPattern = '/^([A-Z][A-Z0-9_]*)(?:\s+(?:ZM|Z|M))?\s*\((.*)\)$/s';
        if (!preg_match($wktTypeWithOptionalModifiersPattern, $wkt, $matches)) {
            throw InvalidGeometryException::forInvalidWktFormat($wkt);
        }

        $typeString = $matches[1];
        $body = $matches[2];
        if ($body === '') {
            throw InvalidGeometryException::forEmptyCoordinateSection();
        }

        $geometryType = WktGeometryType::tryFrom($typeString);
        if ($geometryType === null) {
            throw InvalidGeometryException::forUnsupportedGeometryType($typeString);
        }

        return new self($srid, $geometryType, $body);
    }

    public function __toString(): string
    {
        $typeAndBody = $this->wktType->value.'('.$this->wktBody.')';
        if ($this->srid === null) {
            return $typeAndBody;
        }

        return 'SRID='.$this->srid.';'.$typeAndBody;
    }

    public function getSrid(): ?int
    {
        return $this->srid;
    }

    public function getGeometryType(): WktGeometryType
    {
        return $this->wktType;
    }

    public function getWkt(): string
    {
        return (string) $this;
    }
}

