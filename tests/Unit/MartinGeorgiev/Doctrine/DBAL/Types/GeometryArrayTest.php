<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeometryForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\GeometryArray;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @since 3.5
 */
final class GeometryArrayTest extends TestCase
{
    private GeometryArray $type;

    private AbstractPlatform&MockObject $platform;

    protected function setUp(): void
    {
        $this->type = new GeometryArray();
        $this->platform = $this->createMock(AbstractPlatform::class);
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertEquals('geometry[]', $this->type->getName());
    }

    #[Test]
    public function can_convert_null_to_database_value(): void
    {
        $result = $this->type->convertToDatabaseValue(null, $this->platform);

        $this->assertNull($result);
    }

    #[Test]
    public function can_convert_empty_array_to_database_value(): void
    {
        $result = $this->type->convertToDatabaseValue([], $this->platform);

        $this->assertSame('{}', $result);
    }

    #[DataProvider('provideValidArraysForDatabase')]
    #[Test]
    public function can_convert_valid_arrays_to_database_value(array $phpArray, string $expectedPostgresArray): void
    {
        $result = $this->type->convertToDatabaseValue($phpArray, $this->platform);

        $this->assertSame($expectedPostgresArray, $result);
    }

    /**
     * @return array<string, array{array<WktSpatialData>, string}>
     */
    public static function provideValidArraysForDatabase(): array
    {
        return [
            'single point' => [
                [WktSpatialData::fromWkt('POINT(1 2)')],
                '{POINT(1 2)}',
            ],
            'point with z dimension' => [
                [WktSpatialData::fromWkt('POINT Z(1 2 3)')],
                '{POINT Z(1 2 3)}',
            ],
            'mixed dimensional modifiers' => [
                [
                    WktSpatialData::fromWkt('POINT Z(1 2 3)'),
                    WktSpatialData::fromWkt('LINESTRING M(0 0 1, 1 1 2)'),
                ],
                '{POINT Z(1 2 3),LINESTRING M(0 0 1, 1 1 2)}',
            ],
            'ewkt with srid' => [
                [
                    WktSpatialData::fromWkt('SRID=4326;POINT(-122.4194 37.7749)'),
                    WktSpatialData::fromWkt('SRID=4326;POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))'),
                ],
                '{SRID=4326;POINT(-122.4194 37.7749),SRID=4326;POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))}',
            ],
            'complex zm geometries' => [
                [
                    WktSpatialData::fromWkt('POINT ZM(1 2 3 4)'),
                    WktSpatialData::fromWkt('MULTIPOLYGON ZM(((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1)))'),
                ],
                '{POINT ZM(1 2 3 4),MULTIPOLYGON ZM(((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1)))}',
            ],
            'mixed geometry types' => [
                [
                    WktSpatialData::fromWkt('POINT(0 0)'),
                    WktSpatialData::fromWkt('LINESTRING(0 0, 1 1)'),
                    WktSpatialData::fromWkt('POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))'),
                ],
                '{POINT(0 0),LINESTRING(0 0, 1 1),POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))}',
            ],
            'mixed srid usage' => [
                [
                    WktSpatialData::fromWkt('POINT(0 0)'),
                    WktSpatialData::fromWkt('SRID=4326;POINT(-122 37)'),
                    WktSpatialData::fromWkt('SRID=3857;POINT(1000 2000)'),
                ],
                '{POINT(0 0),SRID=4326;POINT(-122 37),SRID=3857;POINT(1000 2000)}',
            ],
            'complex mixed array' => [
                [
                    WktSpatialData::fromWkt('POINT(0 0)'),
                    WktSpatialData::fromWkt('SRID=4326;POINT Z(1 2 3)'),
                    WktSpatialData::fromWkt('LINESTRING M(0 0 1, 1 1 2)'),
                    WktSpatialData::fromWkt('MULTIPOINT((1 2), (3 4))'),
                    WktSpatialData::fromWkt('GEOMETRYCOLLECTION(POINT(1 2), LINESTRING(0 0, 1 1))'),
                ],
                '{POINT(0 0),SRID=4326;POINT Z(1 2 3),LINESTRING M(0 0 1, 1 1 2),MULTIPOINT((1 2), (3 4)),GEOMETRYCOLLECTION(POINT(1 2), LINESTRING(0 0, 1 1))}',
            ],
        ];
    }

    #[Test]
    public function can_convert_null_to_php_value(): void
    {
        $result = $this->type->convertToPHPValue(null, $this->platform);

        $this->assertNull($result);
    }

    #[Test]
    public function can_convert_empty_postgres_array_to_php_value(): void
    {
        $result = $this->type->convertToPHPValue('{}', $this->platform);

        $this->assertSame([], $result);
    }

    #[DataProvider('provideValidPostgresArraysForPHP')]
    #[Test]
    public function can_convert_valid_postgres_arrays_to_php_value(string $postgresArray, array $expectedPhpArray): void
    {
        $result = $this->type->convertToPHPValue($postgresArray, $this->platform);

        $this->assertIsArray($result);
        $this->assertCount(\count($expectedPhpArray), $result);

        foreach ($result as $index => $item) {
            $this->assertInstanceOf(WktSpatialData::class, $item);
            $this->assertSame($expectedPhpArray[$index], (string) $item);
        }
    }

    /**
     * @return array<string, array{string, array<string>}>
     */
    public static function provideValidPostgresArraysForPHP(): array
    {
        return [
            'single point' => [
                '{POINT(1 2)}',
                ['POINT(1 2)'],
            ],
            'point with z dimension' => [
                '{POINT Z(1 2 3)}',
                ['POINT Z(1 2 3)'],
            ],
            'mixed dimensional modifiers' => [
                '{POINT Z(1 2 3),LINESTRING M(0 0 1, 1 1 2)}',
                ['POINT Z(1 2 3)', 'LINESTRING M(0 0 1, 1 1 2)'],
            ],
            'ewkt with srid' => [
                '{SRID=4326;POINT(-122.4194 37.7749),SRID=4326;POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))}',
                ['SRID=4326;POINT(-122.4194 37.7749)', 'SRID=4326;POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))'],
            ],
            'complex nested geometry' => [
                '{POLYGON((0 0, 0 1, 1 1, 1 0, 0 0)),MULTIPOINT((1 2), (3 4))}',
                ['POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))', 'MULTIPOINT((1 2), (3 4))'],
            ],
            // Normalization coverage for no-space and extra-space dimensional modifiers
            'no-space POINTZ' => [
                '{POINTZ(1 2 3)}',
                ['POINT Z(1 2 3)'],
            ],
            'no-space LINESTRINGM' => [
                '{LINESTRINGM(0 0 1, 1 1 2)}',
                ['LINESTRING M(0 0 1, 1 1 2)'],
            ],
            'no-space POLYGONZM' => [
                '{POLYGONZM((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1))}',
                ['POLYGON ZM((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1))'],
            ],
            'extra space before parentheses' => [
                '{POINT Z (1 2 3)}',
                ['POINT Z(1 2 3)'],
            ],
            'multiple spaces between type and modifier' => [
                '{POINT  Z(1 2 3)}',
                ['POINT Z(1 2 3)'],
            ],
            'srid with extra space before parentheses' => [
                '{SRID=4326;POINT Z (1 2 3)}',
                ['SRID=4326;POINT Z(1 2 3)'],
            ],
            // Quoted array format tests - critical for PostgreSQL compatibility
            'quoted single point' => [
                '{"POINT(1 2)"}',
                ['POINT(1 2)'],
            ],
            'quoted multiple points' => [
                '{"POINT(1 2)","POINT(3 4)","POINT(5 6)"}',
                ['POINT(1 2)', 'POINT(3 4)', 'POINT(5 6)'],
            ],
            'quoted complex geometries' => [
                '{"POINT(1 2)","LINESTRING(0 0, 1 1)","POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))"}',
                ['POINT(1 2)', 'LINESTRING(0 0, 1 1)', 'POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))'],
            ],
            'quoted geometries with dimensional modifiers' => [
                '{"POINT Z(1 2 3)","LINESTRING M(0 0 1, 1 1 2)","POLYGON ZM((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1))"}',
                ['POINT Z(1 2 3)', 'LINESTRING M(0 0 1, 1 1 2)', 'POLYGON ZM((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1))'],
            ],
            'quoted ewkt with srid' => [
                '{"SRID=4326;POINT(-122.4194 37.7749)","SRID=4326;POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))"}',
                ['SRID=4326;POINT(-122.4194 37.7749)', 'SRID=4326;POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))'],
            ],
            'quoted complex nested geometries' => [
                '{"MULTIPOINT((1 2), (3 4))","GEOMETRYCOLLECTION(POINT(1 2), LINESTRING(0 0, 1 1))"}',
                ['MULTIPOINT((1 2), (3 4))', 'GEOMETRYCOLLECTION(POINT(1 2), LINESTRING(0 0, 1 1))'],
            ],
            'quoted circular geometries' => [
                '{"CIRCULARSTRING(0 0, 1 1, 2 0)","COMPOUNDCURVE((0 0, 1 1), CIRCULARSTRING(1 1, 2 0, 3 1))"}',
                ['CIRCULARSTRING(0 0, 1 1, 2 0)', 'COMPOUNDCURVE((0 0, 1 1), CIRCULARSTRING(1 1, 2 0, 3 1))'],
            ],
            'quoted triangle and tin' => [
                '{"TRIANGLE((0 0, 1 0, 0.5 1, 0 0))","TIN(((0 0, 1 0, 0.5 1, 0 0)), ((1 0, 2 0, 1.5 1, 1 0)))"}',
                ['TRIANGLE((0 0, 1 0, 0.5 1, 0 0))', 'TIN(((0 0, 1 0, 0.5 1, 0 0)), ((1 0, 2 0, 1.5 1, 1 0)))'],
            ],
            // Edge cases for array parsing
            'empty quoted array' => [
                '{}',
                [],
            ],
            'single empty quoted item' => [
                '{"POINT(1 2)",""}',
                ['POINT(1 2)'],
            ],
            // Complex nested structures that test bracket depth tracking
            'complex nested with multiple parentheses' => [
                '{MULTIPOLYGON(((0 0, 0 1, 1 1, 1 0, 0 0)), ((2 2, 2 3, 3 3, 3 2, 2 2))),GEOMETRYCOLLECTION(POINT(1 2), MULTILINESTRING((0 0, 1 1), (2 2, 3 3)))}',
                ['MULTIPOLYGON(((0 0, 0 1, 1 1, 1 0, 0 0)), ((2 2, 2 3, 3 3, 3 2, 2 2)))', 'GEOMETRYCOLLECTION(POINT(1 2), MULTILINESTRING((0 0, 1 1), (2 2, 3 3)))'],
            ],
            'polygon with holes' => [
                '{POLYGON((0 0, 0 3, 3 3, 3 0, 0 0), (1 1, 1 2, 2 2, 2 1, 1 1))}',
                ['POLYGON((0 0, 0 3, 3 3, 3 0, 0 0), (1 1, 1 2, 2 2, 2 1, 1 1))'],
            ],
            'quoted polygon with holes' => [
                '{"POLYGON((0 0, 0 3, 3 3, 3 0, 0 0), (1 1, 1 2, 2 2, 2 1, 1 1))"}',
                ['POLYGON((0 0, 0 3, 3 3, 3 0, 0 0), (1 1, 1 2, 2 2, 2 1, 1 1))'],
            ],
        ];
    }

    #[DataProvider('provideBidirectionalTestCases')]
    #[Test]
    public function preserves_data_in_bidirectional_conversion(array $phpArray): void
    {
        // PHP -> Database -> PHP
        $databaseValue = $this->type->convertToDatabaseValue($phpArray, $this->platform);
        $convertedBack = $this->type->convertToPHPValue($databaseValue, $this->platform);

        $this->assertIsArray($convertedBack);
        $this->assertCount(\count($phpArray), $convertedBack);

        foreach ($convertedBack as $index => $item) {
            $this->assertInstanceOf(WktSpatialData::class, $item);
            $originalItem = $phpArray[$index];
            $this->assertInstanceOf(WktSpatialData::class, $originalItem);
            $this->assertSame((string) $originalItem, (string) $item);
        }
    }

    /**
     * @return array<string, array{array<WktSpatialData>}>
     */
    public static function provideBidirectionalTestCases(): array
    {
        return [
            'dimensional modifiers preservation' => [
                [
                    WktSpatialData::fromWkt('POINT Z(1 2 3)'),
                    WktSpatialData::fromWkt('LINESTRING M(0 0 1, 1 1 2)'),
                    WktSpatialData::fromWkt('POLYGON ZM((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1))'),
                ],
            ],
            'srid preservation' => [
                [
                    WktSpatialData::fromWkt('SRID=4326;POINT Z(-122.4194 37.7749 100)'),
                    WktSpatialData::fromWkt('SRID=3857;LINESTRING M(0 0 1, 1000 1000 2)'),
                ],
            ],
            'mixed complex geometries' => [
                [
                    WktSpatialData::fromWkt('GEOMETRYCOLLECTION Z(POINT Z(1 2 3), LINESTRING Z(0 0 1, 1 1 2))'),
                    WktSpatialData::fromWkt('MULTIPOLYGON(((0 0, 0 1, 1 1, 1 0, 0 0)), ((2 2, 2 3, 3 3, 3 2, 2 2)))'),
                ],
            ],
        ];
    }

    #[DataProvider('provideValidationCases')]
    #[Test]
    public function can_validate_array_items_for_database(mixed $item, bool $expected): void
    {
        $this->assertSame($expected, $this->type->isValidArrayItemForDatabase($item));
    }

    /**
     * @return array<string, array{item: mixed, expected: bool}>
     */
    public static function provideValidationCases(): array
    {
        return [
            'valid WktSpatialData' => [
                'item' => WktSpatialData::fromWkt('POINT(1 2)'),
                'expected' => true,
            ],
            'string is invalid' => [
                'item' => 'not a spatial data object',
                'expected' => false,
            ],
            'null is invalid' => [
                'item' => null,
                'expected' => false,
            ],
            'integer is invalid' => [
                'item' => 123,
                'expected' => false,
            ],
            'array is invalid' => [
                'item' => [],
                'expected' => false,
            ],
        ];
    }

    #[Test]
    public function can_transform_array_item_for_php(): void
    {
        $wktString = 'POINT(1 2)';
        $result = $this->type->transformArrayItemForPHP($wktString);

        $this->assertInstanceOf(WktSpatialData::class, $result);
        $this->assertEquals($wktString, (string) $result);
    }

    #[Test]
    public function can_transform_null_array_item_for_php(): void
    {
        $result = $this->type->transformArrayItemForPHP(null);

        $this->assertNull($result);
    }

    #[Test]
    public function throws_exception_for_invalid_type_from_database(): void
    {
        $this->expectException(InvalidGeometryForPHPException::class);
        $this->expectExceptionMessage('must be a Geometry value object');

        $this->type->transformArrayItemForPHP(123);
    }

    #[Test]
    public function throws_exception_for_invalid_format_from_database(): void
    {
        $this->expectException(InvalidGeometryForPHPException::class);
        $this->expectExceptionMessage('Invalid Geometry value object format');

        $this->type->transformArrayItemForPHP('INVALID_WKT_FORMAT');
    }

    #[DataProvider('provideDimensionalModifierNormalization')]
    #[Test]
    public function can_normalize_dimensional_modifiers(string $input, string $expected): void
    {
        $result = $this->type->transformArrayItemForPHP($input);

        $this->assertInstanceOf(WktSpatialData::class, $result);
        $this->assertEquals($expected, (string) $result);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideDimensionalModifierNormalization(): array
    {
        return [
            'no-space POINTZ' => ['POINTZ(1 2 3)', 'POINT Z(1 2 3)'],
            'no-space LINESTRINGM' => ['LINESTRINGM(0 0 1, 1 1 2)', 'LINESTRING M(0 0 1, 1 1 2)'],
            'no-space POLYGONZM' => ['POLYGONZM((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1))', 'POLYGON ZM((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1))'],
            'extra space before parentheses' => ['POINT Z (1 2 3)', 'POINT Z(1 2 3)'],
            'multiple spaces between type and modifier' => ['POINT  Z(1 2 3)', 'POINT Z(1 2 3)'],
            'srid with extra space before parentheses' => ['SRID=4326;POINT Z (1 2 3)', 'SRID=4326;POINT Z(1 2 3)'],
            'srid with no-space modifier' => ['SRID=4326;POINTZ(1 2 3)', 'SRID=4326;POINT Z(1 2 3)'],
            'complex geometry with no-space modifier' => ['MULTIPOLYGONZM(((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1)))', 'MULTIPOLYGON ZM(((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1)))'],
        ];
    }

    #[Test]
    public function throws_exception_for_ewkt_missing_semicolon(): void
    {
        $this->expectException(InvalidGeometryForPHPException::class);

        $this->type->transformArrayItemForPHP('SRID=4326POINT(1 2)');
    }

    #[Test]
    public function can_handle_empty_quoted_array_content(): void
    {
        $result = $this->type->convertToPHPValue('{""}', $this->platform);

        $this->assertSame([], $result);
    }

    #[Test]
    public function can_handle_whitespace_only_array(): void
    {
        $result = $this->type->convertToPHPValue('  ', $this->platform);

        $this->assertSame([], $result);
    }
}
