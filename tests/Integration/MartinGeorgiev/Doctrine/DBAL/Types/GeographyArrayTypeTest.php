<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class GeographyArrayTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'geography[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'GEOGRAPHY[]';
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
            'simple geographic features' => [[
                WktSpatialData::fromWkt('POINT(-122.4194 37.7749)'),
                WktSpatialData::fromWkt('LINESTRING(-122.4194 37.7749, -122.4094 37.7849)'),
                WktSpatialData::fromWkt('POLYGON((-122.5 37.7, -122.5 37.8, -122.4 37.8, -122.4 37.7, -122.5 37.7))'),
                WktSpatialData::fromWkt('GEOMETRYCOLLECTION(POINT(-122.4194 37.7749), LINESTRING(-122.4194 37.7749, -122.4094 37.7849))'),
            ]],
            'geographic features with elevation (z)' => [[
                WktSpatialData::fromWkt('POINT Z(-122.4194 37.7749 100)'),
                WktSpatialData::fromWkt('LINESTRING Z(-122.4194 37.7749 100, -122.4094 37.7849 150)'),
                WktSpatialData::fromWkt('POLYGON Z((-122.5 37.7 0, -122.5 37.8 0, -122.4 37.8 0, -122.4 37.7 0, -122.5 37.7 0))'),
            ]],
            'geographic features with measure (m)' => [[
                WktSpatialData::fromWkt('POINT M(-122.4194 37.7749 1)'),
                WktSpatialData::fromWkt('LINESTRING M(-122.4194 37.7749 1, -122.4094 37.7849 2)'),
                WktSpatialData::fromWkt('MULTIPOINT M((-122.4194 37.7749 1), (-122.4094 37.7849 2))'),
            ]],
            'geographic features with elevation and measure (zm)' => [[
                WktSpatialData::fromWkt('POINT ZM(-122.4194 37.7749 100 1)'),
                WktSpatialData::fromWkt('LINESTRING ZM(-122.4194 37.7749 100 1, -122.4094 37.7849 150 2)'),
                WktSpatialData::fromWkt('POLYGON ZM((-122.5 37.7 0 1, -122.5 37.8 0 1, -122.4 37.8 0 1, -122.4 37.7 0 1, -122.5 37.7 0 1))'),
            ]],
            'geographic features with srid' => [[
                WktSpatialData::fromWkt('SRID=4326;POINT(-122.4194 37.7749)'),
                WktSpatialData::fromWkt('SRID=4326;POLYGON((-122.5 37.7, -122.5 37.8, -122.4 37.8, -122.4 37.7, -122.5 37.7))'),
                WktSpatialData::fromWkt('SRID=4269;LINESTRING(-122.4194 37.7749, -122.4094 37.7849)'),
            ]],
            'mixed geographic features with dimensions and srid' => [[
                WktSpatialData::fromWkt('POINT Z(-122.4194 37.7749 100)'),
                WktSpatialData::fromWkt('LINESTRING M(-122.4194 37.7749 1, -122.4094 37.7849 2)'),
                WktSpatialData::fromWkt('SRID=4326;POINT Z(-122.4194 37.7749 100)'),
                WktSpatialData::fromWkt('SRID=4326;POLYGON ZM((-122.5 37.7 0 1, -122.5 37.8 0 1, -122.4 37.8 0 1, -122.4 37.7 0 1, -122.5 37.7 0 1))'),
            ]],
            'world coordinate edge cases' => [[
                WktSpatialData::fromWkt('POINT(0 0)'),      // Null Island
                WktSpatialData::fromWkt('POINT(180 0)'),    // International Date Line
                WktSpatialData::fromWkt('POINT(-180 0)'),   // International Date Line (other side)
                WktSpatialData::fromWkt('POINT(0 90)'),     // North Pole
                WktSpatialData::fromWkt('POINT(0 -90)'),    // South Pole
            ]],
            'complex geographic multigeometries' => [[
                WktSpatialData::fromWkt('MULTIPOINT Z((-122.4194 37.7749 100), (-122.4094 37.7849 150))'),
                WktSpatialData::fromWkt('MULTILINESTRING M((-122.4194 37.7749 1, -122.4094 37.7849 2), (-122.4294 37.7649 3, -122.4394 37.7549 4))'),
                WktSpatialData::fromWkt('MULTIPOLYGON ZM(((-122.5 37.7 0 1, -122.5 37.8 0 1, -122.4 37.8 0 1, -122.4 37.7 0 1, -122.5 37.7 0 1)))'),
                WktSpatialData::fromWkt('GEOMETRYCOLLECTION Z(POINT Z(-122.4194 37.7749 100), LINESTRING Z(-122.4194 37.7749 100, -122.4094 37.7849 150))'),
            ]],
            'single geographic point' => [[
                WktSpatialData::fromWkt('POINT(-122.4194 37.7749)'),
            ]],
            'empty geographic array' => [[]],
        ];
    }
}
