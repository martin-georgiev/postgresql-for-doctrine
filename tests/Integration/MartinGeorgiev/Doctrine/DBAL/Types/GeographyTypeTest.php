<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class GeographyTypeTest extends TestCase
{
    use OrmEntityPersistenceTrait;
    use WktAssertionTrait;

    protected function getTypeName(): string
    {
        return 'geography';
    }

    protected function getEntityClass(): string
    {
        return ContainsGeometries::class;
    }

    protected function getEntityColumnName(): string
    {
        return 'geography1';
    }

    protected function createTestTableForEntity(string $tableName): void
    {
        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                geometry1 GEOMETRY,
                geometry2 GEOMETRY,
                geography1 GEOGRAPHY,
                geography2 GEOGRAPHY
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);
    }

    protected function assertOrmValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        if (!$expected instanceof WktSpatialData || !$actual instanceof WktSpatialData) {
            throw new \InvalidArgumentException('Expected WktSpatialData value objects.');
        }

        $this->assertWktEquals($expected, $actual);
    }

    #[Test]
    public function roundtrips_null_value(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value(WktSpatialData $wktSpatialData): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $wktSpatialData);
    }

    #[Test]
    public function normalizes_sridless_value_to_srid_4326(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTripExpectingDifferentRetrievedValue(
            $typeName,
            $columnType,
            WktSpatialData::fromString('POINT(-122.4194 37.7749)'),
            WktSpatialData::fromString('SRID=4326;POINT(-122.4194 37.7749)')
        );
    }

    #[Test]
    public function roundtrips_null_value_using_entity_manager_find(): void
    {
        $this->runOrmFindRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value_using_entity_manager_find(WktSpatialData $wktSpatialData): void
    {
        $this->runOrmFindRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $wktSpatialData);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value_using_dql_select(WktSpatialData $wktSpatialData): void
    {
        $this->runOrmDqlSelectRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $wktSpatialData);
    }

    /**
     * @return array<string, array{WktSpatialData}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'point' => [WktSpatialData::fromString('SRID=4326;POINT(1 2)')],
            'linestring' => [WktSpatialData::fromString('SRID=4326;LINESTRING(0 0,1 1,2 2)')],
            'polygon' => [WktSpatialData::fromString('SRID=4326;POLYGON((0 0,0 1,1 1,1 0,0 0))')],
            'geometrycollection' => [WktSpatialData::fromString('SRID=4326;GEOMETRYCOLLECTION(POINT(1 2),LINESTRING(0 0,1 1))')],
            'point with full double precision' => [WktSpatialData::fromString('SRID=4326;POINT(-122.41941234567891 0.00012345678901234567)')],
            'point z' => [WktSpatialData::fromString('SRID=4326;POINT Z(-122.4194 37.7749 100)')],
            'linestring m' => [WktSpatialData::fromString('SRID=4326;LINESTRING M(-122.4194 37.7749 1,-122.4094 37.7849 2)')],
            'polygon zm' => [WktSpatialData::fromString('SRID=4326;POLYGON ZM((-122.5 37.7 0 1,-122.5 37.8 0 1,-122.4 37.8 0 1,-122.4 37.7 0 1,-122.5 37.7 0 1))')],
            'point empty' => [WktSpatialData::fromString('SRID=4326;POINT EMPTY')],
            'polygon empty' => [WktSpatialData::fromString('SRID=4326;POLYGON EMPTY')],
            'geometrycollection empty' => [WktSpatialData::fromString('SRID=4326;GEOMETRYCOLLECTION EMPTY')],
            'point z empty' => [WktSpatialData::fromString('SRID=4326;POINT Z EMPTY')],
        ];
    }
}
