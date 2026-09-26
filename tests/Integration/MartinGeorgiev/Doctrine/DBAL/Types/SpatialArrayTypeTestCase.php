<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsSpatialArrays;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

abstract class SpatialArrayTypeTestCase extends TestCase
{
    use OrmEntityPersistenceTrait;

    protected function getEntityClass(): string
    {
        return ContainsSpatialArrays::class;
    }

    protected function createTestTableForEntity(string $tableName): void
    {
        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                geometries GEOMETRY[],
                geographies GEOGRAPHY[]
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);
    }

    protected function getSelectExpression(string $columnName): string
    {
        $platform = $this->connection->getDatabasePlatform();
        $columnSql = Type::getType($this->getTypeName())->convertToPHPValueSQL(\sprintf('"%s"', $columnName), $platform);

        return \sprintf('%s AS "%s"', $columnSql, $columnName);
    }

    #[Test]
    public function roundtrips_null_value_using_entity_manager_find(): void
    {
        $this->runOrmFindRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), null);
    }

    /**
     * @param array<WktSpatialData> $values
     */
    #[DataProvider('provideSingleItemArrays')]
    #[Test]
    public function roundtrips_value_using_entity_manager_find(array $values): void
    {
        $this->runOrmFindRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $values);
    }

    /**
     * @param array<WktSpatialData> $values
     */
    #[DataProvider('provideSingleItemArrays')]
    #[Test]
    public function roundtrips_value_using_dql_select(array $values): void
    {
        $this->runOrmDqlSelectRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $values);
    }

    /**
     * @param array<int, WktSpatialData|null> $phpValue
     */
    #[DataProvider('provideMultiItemArrays')]
    #[Test]
    public function roundtrips_multi_item_value_using_entity_manager_find(array $phpValue): void
    {
        $this->runOrmFindRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $phpValue);
    }

    /**
     * @param array<int, WktSpatialData|null> $phpValue
     */
    #[DataProvider('provideMultiItemArrays')]
    #[Test]
    public function roundtrips_multi_item_value_using_dql_select(array $phpValue): void
    {
        $this->runOrmDqlSelectRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $phpValue);
    }

    /**
     * Perform a round-trip using ARRAY[...] constructor for insertion for spatial WKT arrays.
     *
     * @param non-empty-string $elementPgType
     */
    protected function runArrayConstructorRoundTrip(string $typeName, string $columnType, string $elementPgType, WktSpatialData ...$spatialData): void
    {
        [$tableName, $columnName] = $this->prepareTestTable($columnType);

        try {
            $placeholders = \implode(',', \array_fill(0, \count($spatialData), '?::'.$elementPgType));
            $sql = \sprintf('INSERT INTO %s.%s ("%s") VALUES (ARRAY[%s])', self::DATABASE_SCHEMA, $tableName, $columnName, $placeholders);
            /** @var list<string> $params */
            $params = \array_values(\array_map(static fn (WktSpatialData $wktSpatialData): string => (string) $wktSpatialData, $spatialData));
            $this->connection->executeStatement($sql, $params);

            $retrieved = $this->fetchConvertedValue($typeName, $tableName, $columnName);
            $this->assertRoundTrip($typeName, $spatialData, $retrieved);
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }

    /**
     * Insert an array of spatial WKTs using ARRAY[...] with per-element casts, then retrieve and assert.
     *
     * @param non-empty-string $elementPgType 'geometry' or 'geography'
     */
    protected function runArrayConstructorTypeTest(string $typeName, string $columnType, string $elementPgType, WktSpatialData ...$spatialData): void
    {
        $this->runArrayConstructorRoundTrip($typeName, $columnType, $elementPgType, ...$spatialData);
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        \assert(\is_array($expected) && \is_array($actual));

        // A null element is a SQL NULL in the column and stays null on both sides of the comparison.
        $toString = static fn (?WktSpatialData $wktSpatialData): ?string => $wktSpatialData instanceof WktSpatialData ? (string) $wktSpatialData : null;

        /** @var list<WktSpatialData|null> $expected */
        /** @var list<string|null> $expectedStrings */
        $expectedStrings = \array_values(\array_map($toString, $expected));

        /** @var list<WktSpatialData|null> $actual */
        /** @var list<string|null> $actualStrings */
        $actualStrings = \array_values(\array_map($toString, $actual));

        $stripDefaultSrid = static fn (?string $wkt): ?string => $wkt !== null && \str_starts_with($wkt, 'SRID=4326;') ? \substr($wkt, 10) : $wkt;

        $expectedStrings = \array_map($stripDefaultSrid, $expectedStrings);
        $actualStrings = \array_map($stripDefaultSrid, $actualStrings);

        $this->assertEquals($expectedStrings, $actualStrings, \sprintf('Array type %s round-trip failed', $typeName));
    }
}
