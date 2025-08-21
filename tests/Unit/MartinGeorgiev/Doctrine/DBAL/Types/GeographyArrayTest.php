<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\GeographyArray;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @since 3.5
 */
final class GeographyArrayTest extends TestCase
{
    private GeographyArray $type;

    private AbstractPlatform&MockObject $platform;

    protected function setUp(): void
    {
        $this->type = new GeographyArray();
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
            'single geographic point' => [
                [WktSpatialData::fromWkt('POINT(-122.4194 37.7749)')],
                '{POINT(-122.4194 37.7749)}',
            ],
            'geographic point with elevation' => [
                [WktSpatialData::fromWkt('POINT Z(-122.4194 37.7749 100)')],
                '{POINT Z(-122.4194 37.7749 100)}',
            ],
            'mixed geographic features with dimensions' => [
                [
                    WktSpatialData::fromWkt('POINT Z(-122.4194 37.7749 100)'),
                    WktSpatialData::fromWkt('LINESTRING M(-122.4194 37.7749 1, -122.4094 37.7849 2)'),
                ],
                '{POINT Z(-122.4194 37.7749 100),LINESTRING M(-122.4194 37.7749 1, -122.4094 37.7849 2)}',
            ],
            'geographic areas with srid' => [
                [
                    WktSpatialData::fromWkt('SRID=4326;POINT(-122.4194 37.7749)'),
                    WktSpatialData::fromWkt('SRID=4326;POLYGON((-122.5 37.7, -122.5 37.8, -122.4 37.8, -122.4 37.7, -122.5 37.7))'),
                ],
                '{SRID=4326;POINT(-122.4194 37.7749),SRID=4326;POLYGON((-122.5 37.7, -122.5 37.8, -122.4 37.8, -122.4 37.7, -122.5 37.7))}',
            ],
            'complex geographic zm features' => [
                [
                    WktSpatialData::fromWkt('POINT ZM(-122.4194 37.7749 100 1)'),
                    WktSpatialData::fromWkt('SRID=4326;POLYGON ZM((-122.5 37.7 0 1, -122.5 37.8 0 1, -122.4 37.8 0 1, -122.4 37.7 0 1, -122.5 37.7 0 1))'),
                ],
                '{POINT ZM(-122.4194 37.7749 100 1),SRID=4326;POLYGON ZM((-122.5 37.7 0 1, -122.5 37.8 0 1, -122.4 37.8 0 1, -122.4 37.7 0 1, -122.5 37.7 0 1))}',
            ],
            'world geographic features' => [
                [
                    WktSpatialData::fromWkt('POINT(0 0)'), // Null Island
                    WktSpatialData::fromWkt('POINT(180 0)'), // International Date Line
                    WktSpatialData::fromWkt('POINT(-180 0)'), // International Date Line (other side)
                ],
                '{POINT(0 0),POINT(180 0),POINT(-180 0)}',
            ],
            'mixed geographic geometry types' => [
                [
                    WktSpatialData::fromWkt('SRID=4326;POINT(-122.4194 37.7749)'),
                    WktSpatialData::fromWkt('SRID=4326;LINESTRING(-122.4194 37.7749, -122.4094 37.7849)'),
                    WktSpatialData::fromWkt('SRID=4326;POLYGON((-122.5 37.7, -122.5 37.8, -122.4 37.8, -122.4 37.7, -122.5 37.7))'),
                    WktSpatialData::fromWkt('SRID=4326;MULTIPOINT((-122.4194 37.7749), (-122.4094 37.7849))'),
                ],
                '{SRID=4326;POINT(-122.4194 37.7749),SRID=4326;LINESTRING(-122.4194 37.7749, -122.4094 37.7849),SRID=4326;POLYGON((-122.5 37.7, -122.5 37.8, -122.4 37.8, -122.4 37.7, -122.5 37.7)),SRID=4326;MULTIPOINT((-122.4194 37.7749), (-122.4094 37.7849))}',
            ],
            'mixed geographic dimensional modifiers' => [
                [
                    WktSpatialData::fromWkt('SRID=4326;POINT(-122.4194 37.7749)'),
                    WktSpatialData::fromWkt('SRID=4326;POINT Z(-122.4194 37.7749 100)'),
                    WktSpatialData::fromWkt('SRID=4326;POINT M(-122.4194 37.7749 1)'),
                    WktSpatialData::fromWkt('SRID=4326;POINT ZM(-122.4194 37.7749 100 1)'),
                ],
                '{SRID=4326;POINT(-122.4194 37.7749),SRID=4326;POINT Z(-122.4194 37.7749 100),SRID=4326;POINT M(-122.4194 37.7749 1),SRID=4326;POINT ZM(-122.4194 37.7749 100 1)}',
            ],
            'complex geographic mix' => [
                [
                    WktSpatialData::fromWkt('POINT(0 0)'), // Null Island (no SRID)
                    WktSpatialData::fromWkt('SRID=4326;POINT Z(-122.4194 37.7749 100)'),
                    WktSpatialData::fromWkt('SRID=4269;LINESTRING M(-122.4194 37.7749 1, -122.4094 37.7849 2)'),
                    WktSpatialData::fromWkt('SRID=4326;MULTIPOINT((-122.4194 37.7749), (-122.4094 37.7849))'),
                ],
                '{POINT(0 0),SRID=4326;POINT Z(-122.4194 37.7749 100),SRID=4269;LINESTRING M(-122.4194 37.7749 1, -122.4094 37.7849 2),SRID=4326;MULTIPOINT((-122.4194 37.7749), (-122.4094 37.7849))}',
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
            'single geographic point' => [
                '{POINT(-122.4194 37.7749)}',
                ['POINT(-122.4194 37.7749)'],
            ],
            'geographic point with elevation' => [
                '{POINT Z(-122.4194 37.7749 100)}',
                ['POINT Z(-122.4194 37.7749 100)'],
            ],
            'mixed geographic features' => [
                '{POINT Z(-122.4194 37.7749 100),LINESTRING M(-122.4194 37.7749 1, -122.4094 37.7849 2)}',
                ['POINT Z(-122.4194 37.7749 100)', 'LINESTRING M(-122.4194 37.7749 1, -122.4094 37.7849 2)'],
            ],
            // Normalization coverage: no-space and spacing variants
            'no-space POINTZ geography' => [
                '{POINTZ(-122.4194 37.7749 100)}',
                ['POINT Z(-122.4194 37.7749 100)'],
            ],
            'no-space LINESTRINGM geography' => [
                '{LINESTRINGM(-122.4194 37.7749 1, -122.4094 37.7849 2)}',
                ['LINESTRING M(-122.4194 37.7749 1, -122.4094 37.7849 2)'],
            ],
            'no-space POLYGONZM geography' => [
                '{POLYGONZM((-122.5 37.7 0 1, -122.5 37.8 0 1, -122.4 37.8 0 1, -122.4 37.7 0 1, -122.5 37.7 0 1))}',
                ['POLYGON ZM((-122.5 37.7 0 1, -122.5 37.8 0 1, -122.4 37.8 0 1, -122.4 37.7 0 1, -122.5 37.7 0 1))'],
            ],
            'extra space before parentheses geography' => [
                '{POINT Z (-122.4194 37.7749 100)}',
                ['POINT Z(-122.4194 37.7749 100)'],
            ],
            'multiple spaces between type and modifier geography' => [
                '{POINT  Z(-122.4194 37.7749 100)}',
                ['POINT Z(-122.4194 37.7749 100)'],
            ],
            'srid with extra space before parentheses geography' => [
                '{SRID=4326;POINT Z (-122.4194 37.7749 100)}',
                ['SRID=4326;POINT Z(-122.4194 37.7749 100)'],
            ],

            'geographic areas with srid' => [
                '{SRID=4326;POINT(-122.4194 37.7749),SRID=4326;POLYGON((-122.5 37.7, -122.5 37.8, -122.4 37.8, -122.4 37.7, -122.5 37.7))}',
                ['SRID=4326;POINT(-122.4194 37.7749)', 'SRID=4326;POLYGON((-122.5 37.7, -122.5 37.8, -122.4 37.8, -122.4 37.7, -122.5 37.7))'],
            ],
            'complex geographic multigeometry' => [
                '{MULTIPOINT((-122.4194 37.7749), (-122.4094 37.7849)),MULTILINESTRING((-122.4194 37.7749, -122.4094 37.7849), (-122.4294 37.7649, -122.4394 37.7549))}',
                ['MULTIPOINT((-122.4194 37.7749), (-122.4094 37.7849))', 'MULTILINESTRING((-122.4194 37.7749, -122.4094 37.7849), (-122.4294 37.7649, -122.4394 37.7549))'],
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
            'geographic dimensional modifiers preservation' => [
                [
                    WktSpatialData::fromWkt('POINT Z(-122.4194 37.7749 100)'),
                    WktSpatialData::fromWkt('LINESTRING M(-122.4194 37.7749 1, -122.4094 37.7849 2)'),
                    WktSpatialData::fromWkt('POLYGON ZM((-122.5 37.7 0 1, -122.5 37.8 0 1, -122.4 37.8 0 1, -122.4 37.7 0 1, -122.5 37.7 0 1))'),
                ],
            ],
            'geographic srid preservation' => [
                [
                    WktSpatialData::fromWkt('SRID=4326;POINT Z(-122.4194 37.7749 100)'),
                    WktSpatialData::fromWkt('SRID=4326;LINESTRING M(-122.4194 37.7749 1, -122.4094 37.7849 2)'),
                ],
            ],
            'world coordinate edge cases' => [
                [
                    WktSpatialData::fromWkt('POINT(-180 -90)'), // Southwest corner
                    WktSpatialData::fromWkt('POINT(180 90)'),   // Northeast corner
                    WktSpatialData::fromWkt('POINT(0 0)'),      // Null Island
                ],
            ],
        ];
    }
}
