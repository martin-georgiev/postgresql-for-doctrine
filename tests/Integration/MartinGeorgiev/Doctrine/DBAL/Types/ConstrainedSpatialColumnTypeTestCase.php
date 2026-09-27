<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception\DriverException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use PHPUnit\Framework\Attributes\Test;

abstract class ConstrainedSpatialColumnTypeTestCase extends TestCase
{
    protected function getFieldDeclaration(): array
    {
        return [
            'geometry_type' => 'POINT',
            'srid' => 4326,
        ];
    }

    protected function getExpectedColumnType(): string
    {
        return \sprintf('%s(POINT,4326)', \strtoupper($this->getTypeName()));
    }

    /**
     * The value a column of this type holds for one spatial value.
     */
    protected function toColumnValue(WktSpatialData $wktSpatialData): mixed
    {
        return $wktSpatialData;
    }

    #[Test]
    public function creates_column_with_postgis_type_modifier(): void
    {
        $this->assertSame($this->getExpectedColumnType(), $this->getPostgresTypeName());
    }

    #[Test]
    public function roundtrips_value_matching_the_declared_geometry_type(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $this->toColumnValue(WktSpatialData::fromString('SRID=4326;POINT(-122.4194 37.7749)')));
    }

    #[Test]
    public function normalizes_sridless_value_to_the_declared_srid(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTripExpectingDifferentRetrievedValue(
            $typeName,
            $columnType,
            $this->toColumnValue(WktSpatialData::fromString('POINT(-122.4194 37.7749)')),
            $this->toColumnValue(WktSpatialData::fromString('SRID=4326;POINT(-122.4194 37.7749)'))
        );
    }

    #[Test]
    public function rejects_value_of_another_geometry_type(): void
    {
        $this->expectException(DriverException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $this->toColumnValue(WktSpatialData::fromString('SRID=4326;LINESTRING(0 0,1 1)')));
    }
}
