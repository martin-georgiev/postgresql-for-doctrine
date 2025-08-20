<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
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
    public function can_convert_null_to_database_value(): void
    {
        $result = $this->type->convertToDatabaseValue(null, $this->platform);

        self::assertNull($result);
    }

    #[Test]
    public function can_convert_empty_array_to_database_value(): void
    {
        $result = $this->type->convertToDatabaseValue([], $this->platform);

        self::assertSame('{}', $result);
    }

    #[DataProvider('provideValidArraysForDatabase')]
    #[Test]
    public function can_convert_valid_arrays_to_database_value(array $phpArray, string $expectedPostgresArray): void
    {
        $result = $this->type->convertToDatabaseValue($phpArray, $this->platform);

        self::assertSame($expectedPostgresArray, $result);
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

        self::assertNull($result);
    }

    #[Test]
    public function can_convert_empty_postgres_array_to_php_value(): void
    {
        $result = $this->type->convertToPHPValue('{}', $this->platform);

        self::assertSame([], $result);
    }

    #[DataProvider('provideValidPostgresArraysForPHP')]
    #[Test]
    public function can_convert_valid_postgres_arrays_to_php_value(string $postgresArray, array $expectedPhpArray): void
    {
        $result = $this->type->convertToPHPValue($postgresArray, $this->platform);

        self::assertIsArray($result);
        self::assertCount(\count($expectedPhpArray), $result);

        foreach ($result as $index => $item) {
            self::assertInstanceOf(WktSpatialData::class, $item);
            self::assertSame($expectedPhpArray[$index], (string) $item);
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
        ];
    }

    #[DataProvider('provideBidirectionalTestCases')]
    #[Test]
    public function preserves_data_in_bidirectional_conversion(array $phpArray): void
    {
        // PHP -> Database -> PHP
        $databaseValue = $this->type->convertToDatabaseValue($phpArray, $this->platform);
        $convertedBack = $this->type->convertToPHPValue($databaseValue, $this->platform);

        self::assertIsArray($convertedBack);
        self::assertCount(\count($phpArray), $convertedBack);

        foreach ($convertedBack as $index => $item) {
            self::assertInstanceOf(WktSpatialData::class, $item);
            $originalItem = $phpArray[$index];
            self::assertInstanceOf(WktSpatialData::class, $originalItem);
            self::assertSame((string) $originalItem, (string) $item);
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
}
