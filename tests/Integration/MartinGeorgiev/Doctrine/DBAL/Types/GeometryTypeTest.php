<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class GeometryTypeTest extends TestCase
{
    use OrmEntityPersistenceTrait;
    use WktAssertionTrait;

    protected function getTypeName(): string
    {
        return 'geometry';
    }

    protected function getEntityClass(): string
    {
        return ContainsGeometries::class;
    }

    protected function getEntityColumnName(): string
    {
        return 'geometry1';
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
            'point' => [WktSpatialData::fromString('POINT(1 2)')],
            'linestring' => [WktSpatialData::fromString('LINESTRING(0 0,1 1,2 2)')],
            'polygon' => [WktSpatialData::fromString('POLYGON((0 0,0 1,1 1,1 0,0 0))')],
            'geometrycollection' => [WktSpatialData::fromString('GEOMETRYCOLLECTION(POINT(1 2),LINESTRING(0 0,1 1))')],
            'point with srid' => [WktSpatialData::fromString('SRID=4326;POINT(-122.4194 37.7749)')],
            'point z' => [WktSpatialData::fromString('POINT Z(1 2 3)')],
            'linestring m' => [WktSpatialData::fromString('LINESTRING M(0 0 1,1 1 2)')],
            'polygon zm' => [WktSpatialData::fromString('POLYGON ZM((0 0 0 1,0 1 0 1,1 1 0 1,1 0 0 1,0 0 0 1))')],
            'point z with srid' => [WktSpatialData::fromString('SRID=4326;POINT Z(-122.4194 37.7749 100)')],
            'point empty' => [WktSpatialData::fromString('POINT EMPTY')],
            'polygon empty' => [WktSpatialData::fromString('POLYGON EMPTY')],
            'geometrycollection empty' => [WktSpatialData::fromString('GEOMETRYCOLLECTION EMPTY')],
            'point empty with srid' => [WktSpatialData::fromString('SRID=4326;POINT EMPTY')],
            'point z empty' => [WktSpatialData::fromString('POINT Z EMPTY')],
        ];
    }
}
