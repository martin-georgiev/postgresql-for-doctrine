<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class GeographyTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'geography';
    }

    protected function getPostgresTypeName(): string
    {
        return 'GEOGRAPHY';
    }

    protected function getSelectExpression(string $columnName): string
    {
        return \sprintf('ST_AsEWKT("%s"::geometry) AS "%s"', $columnName, $columnName);
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $this->runTypeTest($this->getTypeName(), $this->getPostgresTypeName(), null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_geography_values(string $testName, WktSpatialData $wktSpatialData): void
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
            'point z' => ['point z', WktSpatialData::fromWkt('POINT Z(-122.4194 37.7749 100)')],
            'linestring m' => ['linestring m', WktSpatialData::fromWkt('LINESTRING M(-122.4194 37.7749 1, -122.4094 37.7849 2)')],
            'polygon zm' => ['polygon zm', WktSpatialData::fromWkt('POLYGON ZM((-122.5 37.7 0 1, -122.5 37.8 0 1, -122.4 37.8 0 1, -122.4 37.7 0 1, -122.5 37.7 0 1))')],
        ];
    }
}
