<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class GeometryTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'geometry';
    }

    protected function getPostgresTypeName(): string
    {
        return 'GEOMETRY';
    }

    protected function getSelectExpression(string $columnName): string
    {
        return \sprintf('ST_AsEWKT("%s") AS "%s"', $columnName, $columnName);
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $this->runTypeTest($this->getTypeName(), $this->getPostgresTypeName(), null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_geometry_values(string $testName, WktSpatialData $wktSpatialData): void
    {
        $this->runTypeTest($this->getTypeName(), $this->getPostgresTypeName(), $wktSpatialData);
    }

    /**
     * @return array<string, array{string, WktSpatialData}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'point' => ['point', WktSpatialData::fromWkt('POINT(1 2)')],
            'linestring' => ['linestring', WktSpatialData::fromWkt('LINESTRING(0 0, 1 1, 2 2)')],
            'polygon' => ['polygon', WktSpatialData::fromWkt('POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))')],
            'geometrycollection' => ['geometrycollection', WktSpatialData::fromWkt('GEOMETRYCOLLECTION(POINT(1 2), LINESTRING(0 0, 1 1))')],
            'point with srid' => ['point with srid', WktSpatialData::fromWkt('SRID=4326;POINT(-122.4194 37.7749)')],
            'point z' => ['point z', WktSpatialData::fromWkt('POINT Z(1 2 3)')],
            'linestring m' => ['linestring m', WktSpatialData::fromWkt('LINESTRING M(0 0 1, 1 1 2)')],
            'polygon zm' => ['polygon zm', WktSpatialData::fromWkt('POLYGON ZM((0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1))')],
            'point z with srid' => ['point z with srid', WktSpatialData::fromWkt('SRID=4326;POINT Z(-122.4194 37.7749 100)')],
        ];
    }
}
