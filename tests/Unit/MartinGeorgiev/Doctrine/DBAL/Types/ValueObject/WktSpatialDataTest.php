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

        self::assertSame(GeometryType::from($expectedType), $wktSpatialData->getGeometryType());
        self::assertSame($expectedSrid, $wktSpatialData->getSrid());
        self::assertSame($wkt, $wktSpatialData->getWkt());
        self::assertSame($wkt, (string) $wktSpatialData);
    }

    #[DataProvider('provideDimensionalModifierWkt')]
    #[Test]
    public function can_extract_dimensional_modifier(string $wkt, ?DimensionalModifier $expectedModifier): void
    {
        $wktSpatialData = WktSpatialData::fromWkt($wkt);

        self::assertSame($expectedModifier, $wktSpatialData->getDimensionalModifier());
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

        self::assertSame($wkt, $output, 'Dimensional modifier should be preserved in round-trip conversion');
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
        ];
    }
}
