<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeographyForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeographyForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Geography;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GeographyTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Geography $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Geography();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertEquals('geography', $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?WktSpatialData $wktSpatialData, ?string $postgresValue): void
    {
        $this->assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($wktSpatialData, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?WktSpatialData $wktSpatialData, ?string $postgresValue): void
    {
        $result = $this->fixture->convertToPHPValue($postgresValue, $this->platform);
        if (!$wktSpatialData instanceof WktSpatialData) {
            $this->assertNull($result);

            return;
        }

        $this->assertInstanceOf(WktSpatialData::class, $result);
        $this->assertEquals((string) $wktSpatialData, (string) $result);
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
            // Multi-geometry types
            'multipoint' => [
                'wktSpatialData' => WktSpatialData::fromWkt('MULTIPOINT((1 2), (3 4), (5 6))'),
                'postgresValue' => 'MULTIPOINT((1 2), (3 4), (5 6))',
            ],
            'multilinestring' => [
                'wktSpatialData' => WktSpatialData::fromWkt('MULTILINESTRING((0 0, 1 1), (2 2, 3 3))'),
                'postgresValue' => 'MULTILINESTRING((0 0, 1 1), (2 2, 3 3))',
            ],
            'multipolygon' => [
                'wktSpatialData' => WktSpatialData::fromWkt('MULTIPOLYGON(((0 0, 0 1, 1 1, 1 0, 0 0)), ((2 2, 2 3, 3 3, 3 2, 2 2)))'),
                'postgresValue' => 'MULTIPOLYGON(((0 0, 0 1, 1 1, 1 0, 0 0)), ((2 2, 2 3, 3 3, 3 2, 2 2)))',
            ],
            'geometrycollection' => [
                'wktSpatialData' => WktSpatialData::fromWkt('GEOMETRYCOLLECTION(POINT(1 2), LINESTRING(0 0, 1 1))'),
                'postgresValue' => 'GEOMETRYCOLLECTION(POINT(1 2), LINESTRING(0 0, 1 1))',
            ],
            // Circular geometry types (PostGIS extensions)
            'circularstring' => [
                'wktSpatialData' => WktSpatialData::fromWkt('CIRCULARSTRING(0 0, 1 1, 2 0)'),
                'postgresValue' => 'CIRCULARSTRING(0 0, 1 1, 2 0)',
            ],
            'compoundcurve' => [
                'wktSpatialData' => WktSpatialData::fromWkt('COMPOUNDCURVE((0 0, 1 1), CIRCULARSTRING(1 1, 2 0, 3 1))'),
                'postgresValue' => 'COMPOUNDCURVE((0 0, 1 1), CIRCULARSTRING(1 1, 2 0, 3 1))',
            ],
            'curvepolygon' => [
                'wktSpatialData' => WktSpatialData::fromWkt('CURVEPOLYGON(CIRCULARSTRING(0 0, 1 1, 2 0, 0 0))'),
                'postgresValue' => 'CURVEPOLYGON(CIRCULARSTRING(0 0, 1 1, 2 0, 0 0))',
            ],
            'multicurve' => [
                'wktSpatialData' => WktSpatialData::fromWkt('MULTICURVE((0 0, 1 1), CIRCULARSTRING(1 1, 2 0, 3 1))'),
                'postgresValue' => 'MULTICURVE((0 0, 1 1), CIRCULARSTRING(1 1, 2 0, 3 1))',
            ],
            'multisurface' => [
                'wktSpatialData' => WktSpatialData::fromWkt('MULTISURFACE(CURVEPOLYGON(CIRCULARSTRING(0 0, 1 1, 2 0, 0 0)))'),
                'postgresValue' => 'MULTISURFACE(CURVEPOLYGON(CIRCULARSTRING(0 0, 1 1, 2 0, 0 0)))',
            ],
            // Triangle and TIN types
            'triangle' => [
                'wktSpatialData' => WktSpatialData::fromWkt('TRIANGLE((0 0, 1 0, 0.5 1, 0 0))'),
                'postgresValue' => 'TRIANGLE((0 0, 1 0, 0.5 1, 0 0))',
            ],
            'tin' => [
                'wktSpatialData' => WktSpatialData::fromWkt('TIN(((0 0, 1 0, 0.5 1, 0 0)), ((1 0, 2 0, 1.5 1, 1 0)))'),
                'postgresValue' => 'TIN(((0 0, 1 0, 0.5 1, 0 0)), ((1 0, 2 0, 1.5 1, 1 0)))',
            ],
            'polyhedralsurface' => [
                'wktSpatialData' => WktSpatialData::fromWkt('POLYHEDRALSURFACE(((0 0, 0 1, 1 1, 1 0, 0 0)), ((0 0, 0 1, 0 0 1, 0 0)))'),
                'postgresValue' => 'POLYHEDRALSURFACE(((0 0, 0 1, 1 1, 1 0, 0 0)), ((0 0, 0 1, 0 0 1, 0 0)))',
            ],
            // Complex dimensional modifiers
            'multipoint z' => [
                'wktSpatialData' => WktSpatialData::fromWkt('MULTIPOINT Z((1 2 3), (4 5 6))'),
                'postgresValue' => 'MULTIPOINT Z((1 2 3), (4 5 6))',
            ],
            'multilinestring m' => [
                'wktSpatialData' => WktSpatialData::fromWkt('MULTILINESTRING M((0 0 1, 1 1 2), (2 2 3, 3 3 4))'),
                'postgresValue' => 'MULTILINESTRING M((0 0 1, 1 1 2), (2 2 3, 3 3 4))',
            ],
            'multipolygon zm' => [
                'wktSpatialData' => WktSpatialData::fromWkt('MULTIPOLYGON ZM(((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1)))'),
                'postgresValue' => 'MULTIPOLYGON ZM(((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1)))',
            ],
            'geometrycollection z' => [
                'wktSpatialData' => WktSpatialData::fromWkt('GEOMETRYCOLLECTION Z(POINT Z(1 2 3), LINESTRING Z(0 0 1, 1 1 2))'),
                'postgresValue' => 'GEOMETRYCOLLECTION Z(POINT Z(1 2 3), LINESTRING Z(0 0 1, 1 1 2))',
            ],
            // Complex SRID combinations
            'complex geometry with srid' => [
                'wktSpatialData' => WktSpatialData::fromWkt('SRID=4326;MULTIPOLYGON(((0 0, 0 1, 1 1, 1 0, 0 0)), ((2 2, 2 3, 3 3, 3 2, 2 2)))'),
                'postgresValue' => 'SRID=4326;MULTIPOLYGON(((0 0, 0 1, 1 1, 1 0, 0 0)), ((2 2, 2 3, 3 3, 3 2, 2 2)))',
            ],
            'circular geometry with srid' => [
                'wktSpatialData' => WktSpatialData::fromWkt('SRID=4326;CIRCULARSTRING(0 0, 1 1, 2 0)'),
                'postgresValue' => 'SRID=4326;CIRCULARSTRING(0 0, 1 1, 2 0)',
            ],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $phpValue): void
    {
        $this->expectException(InvalidGeographyForPHPException::class);
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
            'array input' => [['not', 'geography']],
            'boolean input' => [true],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $postgresValue): void
    {
        $this->expectException(InvalidGeographyForDatabaseException::class);
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
