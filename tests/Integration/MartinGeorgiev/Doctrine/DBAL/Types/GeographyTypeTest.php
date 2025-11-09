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

    protected function getPostgresTypeName(): string
    {
        return 'GEOGRAPHY';
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

    protected function getSelectExpression(string $columnName): string
    {
        // For geography, avoid adding SRID prefix to preserve original input format
        return \sprintf('ST_AsText("%s"::geometry) AS "%s"', $columnName, $columnName);
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_geography_values(string $testName, WktSpatialData $wktSpatialData): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $wktSpatialData);
    }

    #[Test]
    public function can_retrieve_null_geography_using_entity_manager_find(): void
    {
        $this->runOrmFindRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_retrieve_geography_values_using_entity_manager_find(string $testName, WktSpatialData $wktSpatialData): void
    {
        $this->runOrmFindRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $wktSpatialData);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_retrieve_geography_values_using_dql_select(string $testName, WktSpatialData $wktSpatialData): void
    {
        $this->runOrmDqlSelectRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $wktSpatialData);
    }

    /**
     * @return array<string, array{string, WktSpatialData}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'point' => ['point', WktSpatialData::fromWkt('POINT(1 2)')],
            'linestring' => ['linestring', WktSpatialData::fromWkt('LINESTRING(0 0,1 1,2 2)')],
            'polygon' => ['polygon', WktSpatialData::fromWkt('POLYGON((0 0,0 1,1 1,1 0,0 0))')],
            'geometrycollection' => ['geometrycollection', WktSpatialData::fromWkt('GEOMETRYCOLLECTION(POINT(1 2),LINESTRING(0 0,1 1))')],
            'point z' => ['point z', WktSpatialData::fromWkt('POINT Z(-122.4194 37.7749 100)')],
            'linestring m' => ['linestring m', WktSpatialData::fromWkt('LINESTRING M(-122.4194 37.7749 1,-122.4094 37.7849 2)')],
            'polygon zm' => ['polygon zm', WktSpatialData::fromWkt('POLYGON ZM((-122.5 37.7 0 1,-122.5 37.8 0 1,-122.4 37.8 0 1,-122.4 37.7 0 1,-122.5 37.7 0 1))')],
        ];
    }
}
