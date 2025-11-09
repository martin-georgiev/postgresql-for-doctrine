<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DimensionalModifier;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidWktSpatialDataException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\GeometryType;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class WktSpatialDataTest extends TestCase
{
    #[DataProvider('provideValidWkt')]
    #[Test]
    public function can_create_from_wkt(string $wkt, string $expectedType, ?int $expectedSrid): void
    {
        $wktSpatialData = WktSpatialData::fromWkt($wkt);

        $this->assertSame(GeometryType::from($expectedType), $wktSpatialData->getGeometryType());
        $this->assertSame($expectedSrid, $wktSpatialData->getSrid());
        $this->assertSame($wkt, $wktSpatialData->getWkt());
        $this->assertSame($wkt, (string) $wktSpatialData);
    }

    /**
     * @return array<string, array{string, string, int|null}>
     */
    public static function provideValidWkt(): array
    {
        return [
            'point' => ['POINT(1 2)', 'POINT', null],
            'point with srid' => ['SRID=4326;POINT(-122.4194 37.7749)', 'POINT', 4326],
            'linestring' => ['LINESTRING(0 0, 1 1, 2 2)', 'LINESTRING', null],
            'polygon' => ['POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))', 'POLYGON', null],
            'point z' => ['POINT Z(1 2 3)', 'POINT', null],
            'linestring m' => ['LINESTRING M(0 0 1, 1 1 2)', 'LINESTRING', null],
            'polygon zm' => ['POLYGON ZM((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1))', 'POLYGON', null],
            'point z with srid' => ['SRID=4326;POINT Z(-122.4194 37.7749 100)', 'POINT', 4326],
            'multipoint z' => ['MULTIPOINT Z((1 2 3), (4 5 6))', 'MULTIPOINT', null],
            'geometrycollection m' => ['GEOMETRYCOLLECTION M(POINT M(1 2 3), LINESTRING M(0 0 1, 1 1 2))', 'GEOMETRYCOLLECTION', null],
            // Multi-geometry types
            'multipoint' => ['MULTIPOINT((1 2), (3 4), (5 6))', 'MULTIPOINT', null],
            'multilinestring' => ['MULTILINESTRING((0 0, 1 1), (2 2, 3 3))', 'MULTILINESTRING', null],
            'multipolygon' => ['MULTIPOLYGON(((0 0, 0 1, 1 1, 1 0, 0 0)), ((2 2, 2 3, 3 3, 3 2, 2 2)))', 'MULTIPOLYGON', null],
            'geometrycollection' => ['GEOMETRYCOLLECTION(POINT(1 2), LINESTRING(0 0, 1 1))', 'GEOMETRYCOLLECTION', null],
            // Circular geometry types (PostGIS extensions)
            'circularstring' => ['CIRCULARSTRING(0 0, 1 1, 2 0)', 'CIRCULARSTRING', null],
            'compoundcurve' => ['COMPOUNDCURVE((0 0, 1 1), CIRCULARSTRING(1 1, 2 0, 3 1))', 'COMPOUNDCURVE', null],
            'curvepolygon' => ['CURVEPOLYGON(CIRCULARSTRING(0 0, 1 1, 2 0, 0 0))', 'CURVEPOLYGON', null],
            'multicurve' => ['MULTICURVE((0 0, 1 1), CIRCULARSTRING(1 1, 2 0, 3 1))', 'MULTICURVE', null],
            'multisurface' => ['MULTISURFACE(CURVEPOLYGON(CIRCULARSTRING(0 0, 1 1, 2 0, 0 0)))', 'MULTISURFACE', null],
            // Triangle and TIN types
            'triangle' => ['TRIANGLE((0 0, 1 0, 0.5 1, 0 0))', 'TRIANGLE', null],
            'tin' => ['TIN(((0 0, 1 0, 0.5 1, 0 0)), ((1 0, 2 0, 1.5 1, 1 0)))', 'TIN', null],
            'polyhedralsurface' => ['POLYHEDRALSURFACE(((0 0, 0 1, 1 1, 1 0, 0 0)), ((0 0, 0 1, 0 0 1, 0 0)))', 'POLYHEDRALSURFACE', null],
            // Complex SRID combinations
            'complex geometry with srid' => ['SRID=4326;MULTIPOLYGON(((0 0, 0 1, 1 1, 1 0, 0 0)), ((2 2, 2 3, 3 3, 3 2, 2 2)))', 'MULTIPOLYGON', 4326],
            'circular geometry with srid' => ['SRID=4326;CIRCULARSTRING(0 0, 1 1, 2 0)', 'CIRCULARSTRING', 4326],
            'polygon with holes' => ['POLYGON((0 0, 0 3, 3 3, 3 0, 0 0), (1 1, 1 2, 2 2, 2 1, 1 1))', 'POLYGON', null],
            'complex geometrycollection' => ['GEOMETRYCOLLECTION(POINT(1 2), MULTILINESTRING((0 0, 1 1), (2 2, 3 3)), POLYGON((0 0, 0 1, 1 1, 1 0, 0 0)))', 'GEOMETRYCOLLECTION', null],
        ];
    }

    #[DataProvider('provideDimensionalModifierWkt')]
    #[Test]
    public function can_extract_dimensional_modifier(string $wkt, ?DimensionalModifier $dimensionalModifier): void
    {
        $wktSpatialData = WktSpatialData::fromWkt($wkt);

        $this->assertSame($dimensionalModifier, $wktSpatialData->getDimensionalModifier());
    }

    /**
     * @return array<string, array{string, DimensionalModifier|null}>
     */
    public static function provideDimensionalModifierWkt(): array
    {
        return [
            'point without modifier' => ['POINT(1 2)', null],
            'point with z' => ['POINT Z(1 2 3)', DimensionalModifier::Z],
            'point with m' => ['POINT M(1 2 3)', DimensionalModifier::M],
            'point with zm' => ['POINT ZM(1 2 3 4)', DimensionalModifier::ZM],
            'linestring with z' => ['LINESTRING Z(0 0 1, 1 1 2)', DimensionalModifier::Z],
            'polygon with m' => ['POLYGON M((0 0 1, 0 1 2, 1 1 3, 1 0 4, 0 0 1))', DimensionalModifier::M],
            'multipoint with zm' => ['MULTIPOINT ZM((1 2 3 4), (5 6 7 8))', DimensionalModifier::ZM],
            'srid with z modifier' => ['SRID=4326;POINT Z(-122.4194 37.7749 100)', DimensionalModifier::Z],
            'srid without modifier' => ['SRID=4326;POINT(-122.4194 37.7749)', null],
            // Multi-geometry types with dimensional modifiers
            'multipoint with z' => ['MULTIPOINT Z((1 2 3), (4 5 6))', DimensionalModifier::Z],
            'multilinestring with m' => ['MULTILINESTRING M((0 0 1, 1 1 2), (2 2 3, 3 3 4))', DimensionalModifier::M],
            'multipolygon with zm' => ['MULTIPOLYGON ZM(((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1)))', DimensionalModifier::ZM],
            'geometrycollection with z' => ['GEOMETRYCOLLECTION Z(POINT Z(1 2 3), LINESTRING Z(0 0 1, 1 1 2))', DimensionalModifier::Z],
            // Circular geometry types with dimensional modifiers
            'circularstring with z' => ['CIRCULARSTRING Z(0 0 1, 1 1 2, 2 0 1)', DimensionalModifier::Z],
            'compoundcurve with m' => ['COMPOUNDCURVE M((0 0 1, 1 1 2), CIRCULARSTRING M(1 1 2, 2 0 1, 3 1 2))', DimensionalModifier::M],
            'curvepolygon with zm' => ['CURVEPOLYGON ZM(CIRCULARSTRING ZM(0 0 0 1, 1 1 0 1, 2 0 0 1, 0 0 0 1))', DimensionalModifier::ZM],
            'multicurve with z' => ['MULTICURVE Z((0 0 1, 1 1 2), CIRCULARSTRING Z(1 1 2, 2 0 1, 3 1 2))', DimensionalModifier::Z],
            'multisurface with m' => ['MULTISURFACE M(CURVEPOLYGON M(CIRCULARSTRING M(0 0 1, 1 1 2, 2 0 1, 0 0 1)))', DimensionalModifier::M],
            // Triangle and TIN types with dimensional modifiers
            'triangle with z' => ['TRIANGLE Z((0 0 1, 1 0 1, 0.5 1 2, 0 0 1))', DimensionalModifier::Z],
            'tin with m' => ['TIN M(((0 0 1, 1 0 2, 0.5 1 3, 0 0 1)), ((1 0 2, 2 0 3, 1.5 1 4, 1 0 2)))', DimensionalModifier::M],
            'polyhedralsurface with zm' => ['POLYHEDRALSURFACE ZM(((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1)), ((0 0 0 1, 0 1 0 1, 0 0 1 1, 0 0 0 1)))', DimensionalModifier::ZM],
            // Complex SRID combinations with dimensional modifiers
            'complex geometry with srid and z' => ['SRID=4326;MULTIPOLYGON Z(((0 0 0, 0 1 0, 1 1 0, 1 0 0, 0 0 0)), ((2 2 0, 2 3 0, 3 3 0, 3 2 0, 2 2 0)))', DimensionalModifier::Z],
            'circular geometry with srid and m' => ['SRID=4326;CIRCULARSTRING M(0 0 1, 1 1 2, 2 0 1)', DimensionalModifier::M],
        ];
    }

    #[DataProvider('provideInvalidWkt')]
    #[Test]
    public function throws_exception_for_invalid_wkt(string $invalidWkt): void
    {
        $this->expectException(InvalidWktSpatialDataException::class);
        WktSpatialData::fromWkt($invalidWkt);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidWkt(): array
    {
        return [
            'empty' => [''],
            'missing semicolon after srid' => ['SRID=4326POINT(1 2)'],
            'invalid srid' => ['SRID=abc;POINT(1 2)'],
            'invalid body' => ['POINT()'],
            'invalid format' => ['INVALID_WKT'],
            'unsupported geometry type' => ['UNSUPPORTED(1 2)'],
            'whitespace-only coordinates' => ['POINT(   )'],
        ];
    }

    #[DataProvider('provideDimensionalModifierRoundTripCases')]
    #[Test]
    public function preserves_dimensional_modifiers_in_round_trip(string $wkt): void
    {
        $wktSpatialData = WktSpatialData::fromWkt($wkt);
        $output = (string) $wktSpatialData;

        $this->assertSame($wkt, $output, 'Dimensional modifier should be preserved in round-trip conversion');
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideDimensionalModifierRoundTripCases(): array
    {
        return [
            'point z' => ['POINT Z(1 2 3)'],
            'point m' => ['POINT M(1 2 3)'],
            'point zm' => ['POINT ZM(1 2 3 4)'],
            'linestring z' => ['LINESTRING Z(0 0 1, 1 1 2)'],
            'linestring m' => ['LINESTRING M(0 0 1, 1 1 2)'],
            'linestring zm' => ['LINESTRING ZM(0 0 1 2, 1 1 3 4)'],
            'polygon z' => ['POLYGON Z((0 0 1, 0 1 1, 1 1 1, 1 0 1, 0 0 1))'],
            'polygon m' => ['POLYGON M((0 0 1, 0 1 2, 1 1 3, 1 0 4, 0 0 1))'],
            'polygon zm' => ['POLYGON ZM((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1))'],
            'multipoint z' => ['MULTIPOINT Z((1 2 3), (4 5 6))'],
            'multilinestring m' => ['MULTILINESTRING M((0 0 1, 1 1 2), (2 2 3, 3 3 4))'],
            'multipolygon zm' => ['MULTIPOLYGON ZM(((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1)))'],
            'geometrycollection z' => ['GEOMETRYCOLLECTION Z(POINT Z(1 2 3), LINESTRING Z(0 0 1, 1 1 2))'],
            'srid with point z' => ['SRID=4326;POINT Z(-122.4194 37.7749 100)'],
            'srid with polygon zm' => ['SRID=4326;POLYGON ZM((-122.5 37.7 0 1, -122.5 37.8 0 1, -122.4 37.8 0 1, -122.4 37.7 0 1, -122.5 37.7 0 1))'],
        ];
    }

    #[DataProvider('provideFromComponentsData')]
    #[Test]
    public function can_create_from_components(
        GeometryType $geometryType,
        string $coordinates,
        ?int $srid,
        ?DimensionalModifier $dimensionalModifier,
        string $expectedWkt
    ): void {
        $wktSpatialData = WktSpatialData::fromComponents($geometryType, $coordinates, $srid, $dimensionalModifier);

        $this->assertSame($geometryType, $wktSpatialData->getGeometryType());
        $this->assertSame($srid, $wktSpatialData->getSrid());
        $this->assertSame($dimensionalModifier, $wktSpatialData->getDimensionalModifier());
        $this->assertSame($expectedWkt, $wktSpatialData->getWkt());
    }

    /**
     * @return array<string, array{GeometryType, string, int|null, DimensionalModifier|null, string}>
     */
    public static function provideFromComponentsData(): array
    {
        return [
            'simple point' => [
                GeometryType::POINT,
                '1 2',
                null,
                null,
                'POINT(1 2)',
            ],
            'point with srid' => [
                GeometryType::POINT,
                '-122.4194 37.7749',
                4326,
                null,
                'SRID=4326;POINT(-122.4194 37.7749)',
            ],
            'linestring with dimensional modifier' => [
                GeometryType::LINESTRING,
                '0 0 1, 1 1 2',
                null,
                DimensionalModifier::M,
                'LINESTRING M(0 0 1, 1 1 2)',
            ],
            'polygon with all parameters' => [
                GeometryType::POLYGON,
                '0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1',
                4326,
                DimensionalModifier::ZM,
                'SRID=4326;POLYGON ZM(0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1)',
            ],
        ];
    }

    #[DataProvider('provideInvalidCoordinatesData')]
    #[Test]
    public function from_components_throws_exception_for_invalid_coordinates(string $invalidCoordinates): void
    {
        $this->expectException(InvalidWktSpatialDataException::class);
        WktSpatialData::fromComponents(GeometryType::POINT, $invalidCoordinates);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidCoordinatesData(): array
    {
        return [
            'empty string' => [''],
            'whitespace only' => ['  '],
        ];
    }

    #[DataProvider('providePointData')]
    #[Test]
    public function can_create_point(
        float|int|string $longitude,
        float|int|string $latitude,
        ?int $srid,
        string $expectedWkt
    ): void {
        $wktSpatialData = WktSpatialData::point($longitude, $latitude, $srid);

        $this->assertSame(GeometryType::POINT, $wktSpatialData->getGeometryType());
        $this->assertSame($srid, $wktSpatialData->getSrid());
        $this->assertNull($wktSpatialData->getDimensionalModifier());
        $this->assertSame($expectedWkt, $wktSpatialData->getWkt());
    }

    /**
     * @return array<string, array{float|int|string, float|int|string, int|null, string}>
     */
    public static function providePointData(): array
    {
        return [
            'integer coordinates' => [1, 2, null, 'POINT(1 2)'],
            'float coordinates' => [-122.4194, 37.7749, null, 'POINT(-122.4194 37.7749)'],
            'string coordinates' => ['-122.4194', '37.7749', null, 'POINT(-122.4194 37.7749)'],
            'with srid' => [-122.4194, 37.7749, 4326, 'SRID=4326;POINT(-122.4194 37.7749)'],
        ];
    }

    #[DataProvider('providePoint3dData')]
    #[Test]
    public function can_create_3d_point(
        float|int|string $longitude,
        float|int|string $latitude,
        float|int|string $elevation,
        ?int $srid,
        string $expectedWkt
    ): void {
        $wktSpatialData = WktSpatialData::point3d($longitude, $latitude, $elevation, $srid);

        $this->assertSame(GeometryType::POINT, $wktSpatialData->getGeometryType());
        $this->assertSame($srid, $wktSpatialData->getSrid());
        $this->assertSame(DimensionalModifier::Z, $wktSpatialData->getDimensionalModifier());
        $this->assertSame($expectedWkt, $wktSpatialData->getWkt());
    }

    /**
     * @return array<string, array{float|int|string, float|int|string, float|int|string, int|null, string}>
     */
    public static function providePoint3dData(): array
    {
        return [
            'simple 3d point' => [1, 2, 3, null, 'POINT Z(1 2 3)'],
            'with srid' => [-122.4194, 37.7749, 100, 4326, 'SRID=4326;POINT Z(-122.4194 37.7749 100)'],
        ];
    }
}
