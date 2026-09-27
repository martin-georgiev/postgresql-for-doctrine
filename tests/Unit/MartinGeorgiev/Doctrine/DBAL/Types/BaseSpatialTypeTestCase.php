<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\BaseSpatialType;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class BaseSpatialTypeTestCase extends TestCase
{
    use SpatialColumnOptionsTestTrait;

    /**
     * @var AbstractPlatform&MockObject
     */
    protected MockObject $platform;

    protected BaseSpatialType $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = $this->createFixture();
    }

    abstract protected function createFixture(): BaseSpatialType;

    abstract protected function getExpectedTypeName(): string;

    /**
     * @return class-string<\Throwable>
     */
    abstract protected function getForPHPExceptionClass(): string;

    /**
     * @return class-string<\Throwable>
     */
    abstract protected function getForDatabaseExceptionClass(): string;

    #[Test]
    public function has_name(): void
    {
        $this->assertSame($this->getExpectedTypeName(), $this->fixture->getName());
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_database_value(?WktSpatialData $wktSpatialData, ?string $postgresValue): void
    {
        $this->assertSame($postgresValue, $this->fixture->convertToDatabaseValue($wktSpatialData, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_php_value(?WktSpatialData $wktSpatialData, ?string $postgresValue): void
    {
        $result = $this->fixture->convertToPHPValue($postgresValue, $this->platform);
        if (!$wktSpatialData instanceof WktSpatialData) {
            $this->assertNull($result);

            return;
        }

        $this->assertInstanceOf(WktSpatialData::class, $result);
        $this->assertSame((string) $wktSpatialData, (string) $result);
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
                'wktSpatialData' => WktSpatialData::fromString('POINT(1 2)'),
                'postgresValue' => 'POINT(1 2)',
            ],
            'point with srid' => [
                'wktSpatialData' => WktSpatialData::fromString('SRID=4326;POINT(-122.4194 37.7749)'),
                'postgresValue' => 'SRID=4326;POINT(-122.4194 37.7749)',
            ],
            'linestring' => [
                'wktSpatialData' => WktSpatialData::fromString('LINESTRING(0 0, 1 1, 2 2)'),
                'postgresValue' => 'LINESTRING(0 0, 1 1, 2 2)',
            ],
            'polygon' => [
                'wktSpatialData' => WktSpatialData::fromString('POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))'),
                'postgresValue' => 'POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))',
            ],
            'point z' => [
                'wktSpatialData' => WktSpatialData::fromString('POINT Z(1 2 3)'),
                'postgresValue' => 'POINT Z(1 2 3)',
            ],
            'linestring m' => [
                'wktSpatialData' => WktSpatialData::fromString('LINESTRING M(0 0 1, 1 1 2)'),
                'postgresValue' => 'LINESTRING M(0 0 1, 1 1 2)',
            ],
            'polygon zm' => [
                'wktSpatialData' => WktSpatialData::fromString('POLYGON ZM((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1))'),
                'postgresValue' => 'POLYGON ZM((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1))',
            ],
            'point z with srid' => [
                'wktSpatialData' => WktSpatialData::fromString('SRID=4326;POINT Z(-122.4194 37.7749 100)'),
                'postgresValue' => 'SRID=4326;POINT Z(-122.4194 37.7749 100)',
            ],
            'multipoint' => [
                'wktSpatialData' => WktSpatialData::fromString('MULTIPOINT((1 2), (3 4), (5 6))'),
                'postgresValue' => 'MULTIPOINT((1 2), (3 4), (5 6))',
            ],
            'multilinestring' => [
                'wktSpatialData' => WktSpatialData::fromString('MULTILINESTRING((0 0, 1 1), (2 2, 3 3))'),
                'postgresValue' => 'MULTILINESTRING((0 0, 1 1), (2 2, 3 3))',
            ],
            'multipolygon' => [
                'wktSpatialData' => WktSpatialData::fromString('MULTIPOLYGON(((0 0, 0 1, 1 1, 1 0, 0 0)), ((2 2, 2 3, 3 3, 3 2, 2 2)))'),
                'postgresValue' => 'MULTIPOLYGON(((0 0, 0 1, 1 1, 1 0, 0 0)), ((2 2, 2 3, 3 3, 3 2, 2 2)))',
            ],
            'geometrycollection' => [
                'wktSpatialData' => WktSpatialData::fromString('GEOMETRYCOLLECTION(POINT(1 2), LINESTRING(0 0, 1 1))'),
                'postgresValue' => 'GEOMETRYCOLLECTION(POINT(1 2), LINESTRING(0 0, 1 1))',
            ],
            'circularstring' => [
                'wktSpatialData' => WktSpatialData::fromString('CIRCULARSTRING(0 0, 1 1, 2 0)'),
                'postgresValue' => 'CIRCULARSTRING(0 0, 1 1, 2 0)',
            ],
            'compoundcurve' => [
                'wktSpatialData' => WktSpatialData::fromString('COMPOUNDCURVE((0 0, 1 1), CIRCULARSTRING(1 1, 2 0, 3 1))'),
                'postgresValue' => 'COMPOUNDCURVE((0 0, 1 1), CIRCULARSTRING(1 1, 2 0, 3 1))',
            ],
            'curvepolygon' => [
                'wktSpatialData' => WktSpatialData::fromString('CURVEPOLYGON(CIRCULARSTRING(0 0, 1 1, 2 0, 0 0))'),
                'postgresValue' => 'CURVEPOLYGON(CIRCULARSTRING(0 0, 1 1, 2 0, 0 0))',
            ],
            'multicurve' => [
                'wktSpatialData' => WktSpatialData::fromString('MULTICURVE((0 0, 1 1), CIRCULARSTRING(1 1, 2 0, 3 1))'),
                'postgresValue' => 'MULTICURVE((0 0, 1 1), CIRCULARSTRING(1 1, 2 0, 3 1))',
            ],
            'multisurface' => [
                'wktSpatialData' => WktSpatialData::fromString('MULTISURFACE(CURVEPOLYGON(CIRCULARSTRING(0 0, 1 1, 2 0, 0 0)))'),
                'postgresValue' => 'MULTISURFACE(CURVEPOLYGON(CIRCULARSTRING(0 0, 1 1, 2 0, 0 0)))',
            ],
            'triangle' => [
                'wktSpatialData' => WktSpatialData::fromString('TRIANGLE((0 0, 1 0, 0.5 1, 0 0))'),
                'postgresValue' => 'TRIANGLE((0 0, 1 0, 0.5 1, 0 0))',
            ],
            'tin' => [
                'wktSpatialData' => WktSpatialData::fromString('TIN(((0 0, 1 0, 0.5 1, 0 0)), ((1 0, 2 0, 1.5 1, 1 0)))'),
                'postgresValue' => 'TIN(((0 0, 1 0, 0.5 1, 0 0)), ((1 0, 2 0, 1.5 1, 1 0)))',
            ],
            'polyhedralsurface' => [
                'wktSpatialData' => WktSpatialData::fromString('POLYHEDRALSURFACE(((0 0, 0 1, 1 1, 1 0, 0 0)), ((0 0, 0 1, 0 0 1, 0 0)))'),
                'postgresValue' => 'POLYHEDRALSURFACE(((0 0, 0 1, 1 1, 1 0, 0 0)), ((0 0, 0 1, 0 0 1, 0 0)))',
            ],
            'multipoint z' => [
                'wktSpatialData' => WktSpatialData::fromString('MULTIPOINT Z((1 2 3), (4 5 6))'),
                'postgresValue' => 'MULTIPOINT Z((1 2 3), (4 5 6))',
            ],
            'multilinestring m' => [
                'wktSpatialData' => WktSpatialData::fromString('MULTILINESTRING M((0 0 1, 1 1 2), (2 2 3, 3 3 4))'),
                'postgresValue' => 'MULTILINESTRING M((0 0 1, 1 1 2), (2 2 3, 3 3 4))',
            ],
            'multipolygon zm' => [
                'wktSpatialData' => WktSpatialData::fromString('MULTIPOLYGON ZM(((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1)))'),
                'postgresValue' => 'MULTIPOLYGON ZM(((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1)))',
            ],
            'geometrycollection z' => [
                'wktSpatialData' => WktSpatialData::fromString('GEOMETRYCOLLECTION Z(POINT Z(1 2 3), LINESTRING Z(0 0 1, 1 1 2))'),
                'postgresValue' => 'GEOMETRYCOLLECTION Z(POINT Z(1 2 3), LINESTRING Z(0 0 1, 1 1 2))',
            ],
            'complex geometry with srid' => [
                'wktSpatialData' => WktSpatialData::fromString('SRID=4326;MULTIPOLYGON(((0 0, 0 1, 1 1, 1 0, 0 0)), ((2 2, 2 3, 3 3, 3 2, 2 2)))'),
                'postgresValue' => 'SRID=4326;MULTIPOLYGON(((0 0, 0 1, 1 1, 1 0, 0 0)), ((2 2, 2 3, 3 3, 3 2, 2 2)))',
            ],
            'circular geometry with srid' => [
                'wktSpatialData' => WktSpatialData::fromString('SRID=4326;CIRCULARSTRING(0 0, 1 1, 2 0)'),
                'postgresValue' => 'SRID=4326;CIRCULARSTRING(0 0, 1 1, 2 0)',
            ],
            'point empty' => [
                'wktSpatialData' => WktSpatialData::fromString('POINT EMPTY'),
                'postgresValue' => 'POINT EMPTY',
            ],
            'polygon empty' => [
                'wktSpatialData' => WktSpatialData::fromString('POLYGON EMPTY'),
                'postgresValue' => 'POLYGON EMPTY',
            ],
            'geometrycollection empty' => [
                'wktSpatialData' => WktSpatialData::fromString('GEOMETRYCOLLECTION EMPTY'),
                'postgresValue' => 'GEOMETRYCOLLECTION EMPTY',
            ],
            'point z empty with srid' => [
                'wktSpatialData' => WktSpatialData::fromString('SRID=4326;POINT Z EMPTY'),
                'postgresValue' => 'SRID=4326;POINT Z EMPTY',
            ],
        ];
    }

    #[DataProvider('provideGluedDimensionalModifierPostgresValues')]
    #[Test]
    public function normalizes_glued_dimensional_modifier_for_php_value(string $postgresValue, string $expectedWkt): void
    {
        $result = $this->fixture->convertToPHPValue($postgresValue, $this->platform);

        $this->assertInstanceOf(WktSpatialData::class, $result);
        $this->assertSame($expectedWkt, (string) $result);
    }

    /**
     * @return array<string, array{postgresValue: string, expectedWkt: string}>
     */
    public static function provideGluedDimensionalModifierPostgresValues(): array
    {
        return [
            'point m' => ['postgresValue' => 'POINTM(1 2 3)', 'expectedWkt' => 'POINT M(1 2 3)'],
            'linestring m with srid' => ['postgresValue' => 'SRID=4326;LINESTRINGM(0 0 1, 1 1 2)', 'expectedWkt' => 'SRID=4326;LINESTRING M(0 0 1, 1 1 2)'],
        ];
    }

    /**
     * @param array<string, mixed> $fieldDeclaration
     */
    protected function getSQLDeclarationFor(array $fieldDeclaration): string
    {
        return $this->fixture->getSQLDeclaration($fieldDeclaration, $this->platform);
    }

    protected function getExpectedSQLDeclaration(string $typeModifier): string
    {
        return \strtoupper($this->getExpectedTypeName()).$typeModifier;
    }

    #[Test]
    public function wraps_sql_expression_for_ewkt_conversion(): void
    {
        $sql = $this->fixture->convertToPHPValueSQL('geom_col', $this->platform);

        $this->assertSame(
            "CASE WHEN ST_SRID(geom_col) = 0 THEN ST_AsText(geom_col, 25) ELSE 'SRID=' || ST_SRID(geom_col) || ';' || ST_AsText(geom_col, 25) END",
            $sql
        );
    }

    #[Test]
    public function returns_true_for_sql_conversion_requirement(): void
    {
        $this->assertTrue($this->fixture->canRequireSQLConversion());
    }

    #[DataProvider('provideInvalidInputValues')]
    #[Test]
    public function throws_exception_for_invalid_php_value_when_converting_to_database_value(mixed $phpValue): void
    {
        $this->expectException($this->getForDatabaseExceptionClass());
        $this->fixture->convertToDatabaseValue($phpValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidInputValues(): array
    {
        return [
            'random string' => ['foo'],
            'integer input' => [123],
            'array input' => [['not a spatial object']],
            'boolean input' => [true],
            'object input' => [new \stdClass()],
        ];
    }

    #[DataProvider('provideInvalidDatabaseValues')]
    #[Test]
    public function throws_exception_for_invalid_database_value_when_converting_to_php_value(mixed $postgresValue): void
    {
        $this->expectException($this->getForPHPExceptionClass());
        $this->fixture->convertToPHPValue($postgresValue, $this->platform);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidDatabaseValues(): array
    {
        return [
            'empty string' => [''],
            'invalid wkt' => ['INVALID_WKT'],
            'missing coordinates' => ['POINT()'],
            'not a string' => [123],
        ];
    }
}
