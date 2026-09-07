<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception\DriverException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use PHPUnit\Framework\Attributes\Test;

/**
 * Guards the spatial column options end-to-end: the test column is created from the
 * production `getSQLDeclaration()` output, so a regression that drops the PostGIS type
 * modifier would leave an unconstrained column behind and make
 * `rejects_value_of_another_geometry_type()` fail.
 */
abstract class ConstrainedSpatialColumnTypeTestCase extends TestCase
{
    protected function getFieldDeclaration(): array
    {
        return [
            'geometry_type' => 'POINT',
            'srid' => 4326,
        ];
    }

    protected function getSelectExpression(string $columnName): string
    {
        return \sprintf(
            'CASE WHEN ST_SRID("%s") = 0 THEN ST_AsText("%s") ELSE '
            ."'SRID=' || ST_SRID(\"%s\") || ';' || ST_AsText(\"%s\") END AS \"%s\"",
            $columnName,
            $columnName,
            $columnName,
            $columnName,
            $columnName
        );
    }

    #[Test]
    public function declares_column_with_postgis_type_modifier(): void
    {
        $this->assertSame(
            \sprintf('%s(POINT,4326)', \strtoupper($this->getTypeName())),
            $this->getPostgresTypeName()
        );
    }

    #[Test]
    public function roundtrips_value_matching_the_declared_geometry_type(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, WktSpatialData::fromWkt('SRID=4326;POINT(-122.4194 37.7749)'));
    }

    #[Test]
    public function rejects_value_of_another_geometry_type(): void
    {
        $this->expectException(DriverException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, WktSpatialData::fromWkt('SRID=4326;LINESTRING(0 0,1 1)'));
    }
}
