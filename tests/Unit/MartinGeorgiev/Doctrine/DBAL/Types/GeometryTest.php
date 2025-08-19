<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeometryForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeometryForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Geometry;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GeometryTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Geometry $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Geometry();
    }

    #[Test]
    public function has_name(): void
    {
        self::assertEquals('geometry', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?WktSpatialData $wktSpatialData, ?string $postgresValue): void
    {
        self::assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($wktSpatialData, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?WktSpatialData $wktSpatialData, ?string $postgresValue): void
    {
        $result = $this->fixture->convertToPHPValue($postgresValue, $this->platform);
        if (!$wktSpatialData instanceof WktSpatialData) {
            self::assertNull($result);

            return;
        }

        self::assertInstanceOf(WktSpatialData::class, $result);
        self::assertEquals((string) $wktSpatialData, (string) $result);
    }

    /**
     * @return array<string, array{wktSpatialData: WktSpatialData|null, postgresValue: string|null}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'null' => [
                'wktSpatialData' => null,
                'postgresValue' => null,
            ],
            'point' => [
                'wktSpatialData' => WktSpatialData::fromWkt('POINT(1 2)'),
                'postgresValue' => 'POINT(1 2)',
            ],
            'point with srid' => [
                'wktSpatialData' => WktSpatialData::fromWkt('SRID=4326;POINT(-122.4194 37.7749)'),
                'postgresValue' => 'SRID=4326;POINT(-122.4194 37.7749)',
            ],
            'linestring' => [
                'wktSpatialData' => WktSpatialData::fromWkt('LINESTRING(0 0, 1 1, 2 2)'),
                'postgresValue' => 'LINESTRING(0 0, 1 1, 2 2)',
            ],
            'polygon' => [
                'wktSpatialData' => WktSpatialData::fromWkt('POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))'),
                'postgresValue' => 'POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))',
            ],
            'point z' => [
                'wktSpatialData' => WktSpatialData::fromWkt('POINT Z(1 2 3)'),
                'postgresValue' => 'POINT Z(1 2 3)',
            ],
            'linestring m' => [
                'wktSpatialData' => WktSpatialData::fromWkt('LINESTRING M(0 0 1, 1 1 2)'),
                'postgresValue' => 'LINESTRING M(0 0 1, 1 1 2)',
            ],
            'polygon zm' => [
                'wktSpatialData' => WktSpatialData::fromWkt('POLYGON ZM((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1))'),
                'postgresValue' => 'POLYGON ZM((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1))',
            ],
            'point z with srid' => [
                'wktSpatialData' => WktSpatialData::fromWkt('SRID=4326;POINT Z(-122.4194 37.7749 100)'),
                'postgresValue' => 'SRID=4326;POINT Z(-122.4194 37.7749 100)',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidGeometryForPHPException::class);
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValueInputs(): array
    {
        return [
            'random string' => ['foo'],
            'integer input' => [123],
            'array input' => [['not', 'geometry']],
            'boolean input' => [true],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $postgresValue): void
    {
        $this->expectException(InvalidGeometryForDatabaseException::class);
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidPHPValueInputs(): array
    {
        return [
            'empty string' => [''],
            'invalid wkt' => ['INVALID_WKT'],
            'missing coordinates' => ['POINT()'],
            'not a string' => [123],
        ];
    }
}
