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

    #[DataProvider('provideSingleItemArrays')]
    #[Test]
    public function can_handle_single_item_array(array $values): void
    {
        $this->runTypeTest($this->getTypeName(), $this->getPostgresTypeName(), $values);
    }

    public static function provideSingleItemArrays(): array
    {
        return [
            // Single item tests - These work perfectly with Doctrine DBAL parameter binding
            'single point' => [[
                WktSpatialData::fromWkt('SRID=4326;POINT(-122.4194 37.7749)'),
            ]],
            'single point with z dimension' => [[
                WktSpatialData::fromWkt('SRID=4326;POINT Z(-122.4194 37.7749 100)'),
            ]],
            'single point with m dimension' => [[
                WktSpatialData::fromWkt('SRID=4326;POINT M(-122.4194 37.7749 1)'),
            ]],
            'single point with zm dimension' => [[
                WktSpatialData::fromWkt('SRID=4326;POINT ZM(-122.4194 37.7749 100 1)'),
            ]],
            'single linestring' => [[
                WktSpatialData::fromWkt('SRID=4326;LINESTRING(-122.4194 37.7749,-122.4094 37.7849,-122.4 37.79)'),
            ]],
            'single polygon' => [[
                WktSpatialData::fromWkt('SRID=4326;POLYGON((-122.5 37.7,-122.5 37.8,-122.4 37.8,-122.4 37.7,-122.5 37.7))'),
            ]],
            'single multipoint' => [[
                WktSpatialData::fromWkt('SRID=4326;MULTIPOINT((-122.4194 37.7749),(-122.4094 37.7849))'),
            ]],
            'world coordinate null island' => [[
                WktSpatialData::fromWkt('SRID=4326;POINT(0 0)'),
            ]],
            'world coordinate north pole' => [[
                WktSpatialData::fromWkt('SRID=4326;POINT(0 90)'),
            ]],
            'world coordinate south pole' => [[
                WktSpatialData::fromWkt('SRID=4326;POINT(0 -90)'),
            ]],

            // Edge cases
            'empty array' => [[]],
        ];
    }

    /**
     * @param array<WktSpatialData> $phpArray
     */
    #[DataProvider('provideMultiItemArrays')]
    #[Test]
    public function can_handle_multi_item_array(array $phpArray): void
    {
        $wkts = \array_values(\array_map(static fn ($v): string => (string) $v, $phpArray));
        $this->runArrayConstructorTypeTest($this->getTypeName(), $this->getPostgresTypeName(), $wkts, 'geography');
    }

    /**
     * @return array<string, array{array<WktSpatialData>}>
     */
    public static function provideMultiItemArrays(): array
    {
        return [
            'two points' => [[
                WktSpatialData::fromWkt('POINT(-122.4194 37.7749)'),
                WktSpatialData::fromWkt('POINT(-122.4094 37.7849)'),
            ]],
            'dimensional modifiers' => [[
                WktSpatialData::fromWkt('SRID=4326;POINT Z(-122.4194 37.7749 100)'),
                WktSpatialData::fromWkt('SRID=4326;POINT M(-122.4194 37.7749 1)'),
                WktSpatialData::fromWkt('SRID=4326;POINT ZM(-122.4194 37.7749 100 1)'),
            ]],
            'mixed types' => [[
                WktSpatialData::fromWkt('SRID=4326;POINT(-122.4194 37.7749)'),
                WktSpatialData::fromWkt('SRID=4326;POINT Z(-122.4194 37.7749 100)'),
                WktSpatialData::fromWkt('SRID=4326;LINESTRING(-122.4194 37.7749,-122.4094 37.7849)'),
                WktSpatialData::fromWkt('SRID=4326;POLYGON((-122.5 37.7,-122.5 37.8,-122.4 37.8,-122.4 37.7,-122.5 37.7))'),
                WktSpatialData::fromWkt('SRID=4326;MULTIPOINT((-122.4194 37.7749),(-122.4094 37.7849))'),
            ]],
        ];
    }
}
