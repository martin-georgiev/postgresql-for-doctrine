<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class GeometryArrayTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'geometry[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'GEOMETRY[]';
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $this->runTypeTest($this->getTypeName(), $this->getPostgresTypeName(), null);
    }

    #[DataProvider('provideValues')]
    #[Test]
    public function can_handle_values(array $values): void
    {
        $this->runTypeTest($this->getTypeName(), $this->getPostgresTypeName(), $values);
    }

    public static function provideValues(): array
    {
        return [
            'simple geometries' => [[
                WktSpatialData::fromWkt('POINT(0 0)'),
                WktSpatialData::fromWkt('LINESTRING(0 0, 1 1)'),
                WktSpatialData::fromWkt('POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))'),
                WktSpatialData::fromWkt('GEOMETRYCOLLECTION(POINT(1 2), LINESTRING(0 0, 1 1))'),
            ]],
            'dimensional modifiers z' => [[
                WktSpatialData::fromWkt('POINT Z(1 2 3)'),
                WktSpatialData::fromWkt('LINESTRING Z(0 0 1, 1 1 2)'),
                WktSpatialData::fromWkt('POLYGON Z((0 0 1, 0 1 1, 1 1 1, 1 0 1, 0 0 1))'),
            ]],
            'dimensional modifiers m' => [[
                WktSpatialData::fromWkt('POINT M(1 2 3)'),
                WktSpatialData::fromWkt('LINESTRING M(0 0 1, 1 1 2)'),
                WktSpatialData::fromWkt('MULTIPOINT M((1 2 3), (4 5 6))'),
            ]],
            'dimensional modifiers zm' => [[
                WktSpatialData::fromWkt('POINT ZM(1 2 3 4)'),
                WktSpatialData::fromWkt('LINESTRING ZM(0 0 1 2, 1 1 3 4)'),
                WktSpatialData::fromWkt('POLYGON ZM((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1))'),
            ]],
            'ewkt with srid' => [[
                WktSpatialData::fromWkt('SRID=4326;POINT(-122.4194 37.7749)'),
                WktSpatialData::fromWkt('SRID=4326;POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))'),
                WktSpatialData::fromWkt('SRID=3857;LINESTRING(0 0, 1000 1000)'),
            ]],
            'mixed dimensional and srid' => [[
                WktSpatialData::fromWkt('POINT Z(1 2 3)'),
                WktSpatialData::fromWkt('LINESTRING M(0 0 1, 1 1 2)'),
                WktSpatialData::fromWkt('SRID=4326;POINT Z(-122.4194 37.7749 100)'),
                WktSpatialData::fromWkt('POLYGON ZM((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1))'),
            ]],
            'complex multigeometries' => [[
                WktSpatialData::fromWkt('MULTIPOINT Z((1 2 3), (4 5 6))'),
                WktSpatialData::fromWkt('MULTILINESTRING M((0 0 1, 1 1 2), (2 2 3, 3 3 4))'),
                WktSpatialData::fromWkt('MULTIPOLYGON ZM(((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1)))'),
                WktSpatialData::fromWkt('GEOMETRYCOLLECTION Z(POINT Z(1 2 3), LINESTRING Z(0 0 1, 1 1 2))'),
            ]],
            'single item array' => [[
                WktSpatialData::fromWkt('POINT(42 42)'),
            ]],
            'empty array' => [[]],
        ];
    }
}
