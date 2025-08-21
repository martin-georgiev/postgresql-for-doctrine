<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class GeometryArrayTypeTest extends SpatialArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'geometry[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'GEOMETRY[]';
    }

    protected function getSelectExpression(string $columnName): string
    {
        return \sprintf(
            'ARRAY(SELECT CASE WHEN ST_SRID(geom) = 0 THEN ST_AsText(geom) ELSE \"SRID=\" || ST_SRID(geom) || \";\" || ST_AsText(geom) END FROM unnest(\"%s\") AS geom) AS \"%s\"',
            $columnName,
            $columnName
        );
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
                WktSpatialData::fromWkt('POINT(0 0)'),
            ]],
            'single point with z dimension' => [[
                WktSpatialData::fromWkt('POINT Z(1 2 3)'),
            ]],
            'single point with m dimension' => [[
                WktSpatialData::fromWkt('POINT M(1 2 3)'),
            ]],
            'single point with zm dimensions' => [[
                WktSpatialData::fromWkt('POINT ZM(1 2 3 4)'),
            ]],
            'single ewkt with srid' => [[
                WktSpatialData::fromWkt('SRID=4326;POINT(0 0)'),
            ]],
            'single ewkt with srid and dimensions' => [[
                WktSpatialData::fromWkt('SRID=4326;POINT Z(1 2 3)'),
            ]],
            'single linestring' => [[
                WktSpatialData::fromWkt('LINESTRING(0 0,1 1,2 2)'),
            ]],
            'single polygon' => [[
                WktSpatialData::fromWkt('POLYGON((0 0,0 1,1 1,1 0,0 0))'),
            ]],
            'single multipoint' => [[
                WktSpatialData::fromWkt('MULTIPOINT((1 2),(3 4))'),
            ]],
            'single complex geometry with srid' => [[
                WktSpatialData::fromWkt('SRID=4326;POLYGON((-122.5 37.7,-122.5 37.8,-122.4 37.8,-122.4 37.7,-122.5 37.7))'),
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
        $wktsAsString = \array_values(\array_map(static fn ($v): string => (string) $v, $phpArray));
        $this->runArrayConstructorTypeTest($this->getTypeName(), $this->getPostgresTypeName(), $wktsAsString, 'geometry');
    }

    /**
     * @return array<string, array{array<WktSpatialData>}>
     */
    public static function provideMultiItemArrays(): array
    {
        return [
            'two points' => [[
                WktSpatialData::fromWkt('POINT(0 0)'),
                WktSpatialData::fromWkt('POINT(1 1)'),
            ]],
            'dimensional modifiers' => [[
                WktSpatialData::fromWkt('POINT Z(1 2 3)'),
                WktSpatialData::fromWkt('POINT M(1 2 3)'),
                WktSpatialData::fromWkt('POINT ZM(1 2 3 4)'),
            ]],
            'mixed types' => [[
                WktSpatialData::fromWkt('POINT(0 0)'),
                WktSpatialData::fromWkt('SRID=4326;POINT Z(1 2 3)'),
                WktSpatialData::fromWkt('LINESTRING M(0 0 1,1 1 2)'),
                WktSpatialData::fromWkt('SRID=3857;POLYGON((0 0,0 1000,1000 1000,1000 0,0 0))'),
                WktSpatialData::fromWkt('MULTIPOINT((1 2),(3 4))'),
            ]],
        ];
    }
}
