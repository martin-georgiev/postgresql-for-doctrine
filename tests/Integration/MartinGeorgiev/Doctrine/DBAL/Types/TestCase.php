<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\Attributes\Test;
use Tests\Integration\MartinGeorgiev\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * The DBAL type name to test (e.g., 'inet', 'text[]', etc.).
     */
    abstract protected function getTypeName(): string;

    /**
     * The PostgreSQL column type name.
     */
    abstract protected function getPostgresTypeName(): string;

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        match (true) {
            \is_array($expected) && \is_array($actual) => $this->assertEquals($expected, $actual, \sprintf('Array type %s round-trip failed', $typeName)),
            default => $this->assertEquals($expected, $actual, \sprintf('Type %s round-trip failed', $typeName))
        };
    }

    protected function runTypeTest(string $typeName, string $columnType, mixed $testValue): void
    {
        $tableName = 'test_type_'.\strtolower(\str_replace([' ', '[]', '()'], ['_', '_array', ''], $columnType));
        $columnName = 'test_column';

        try {
            $this->createTestTableForDataType($tableName, $columnName, $columnType);

            // Insert test value using proper type conversion
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->insert(self::DATABASE_SCHEMA.'.'.$tableName)
                ->values([$columnName => ':value'])
                ->setParameter('value', $testValue, $typeName);

            $queryBuilder->executeStatement();

            // Query the value back
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->select($this->getSelectExpressionForType($typeName, $columnName))
                ->from(self::DATABASE_SCHEMA.'.'.$tableName)
                ->where('id = 1');

            $result = $queryBuilder->executeQuery();
            $row = $result->fetchAssociative();
            \assert(\is_array($row) && \array_key_exists($columnName, $row));

            // Get the value with the correct type
            $platform = $this->connection->getDatabasePlatform();
            $retrievedValue = Type::getType($typeName)->convertToPHPValue($row[$columnName], $platform);

            if ($testValue === null) {
                $this->assertNull($retrievedValue);

                return;
            }

            $this->assertTypeValueEquals($testValue, $retrievedValue, $typeName);
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }

    protected function createTestTableForDataType(string $tableName, string $columnName, string $columnType): void
    {
        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf(
            'CREATE TABLE %s (id SERIAL PRIMARY KEY, "%s" %s)',
            $fullTableName,
            $columnName,
            $columnType
        );

        $this->connection->executeStatement($sql);
    }

    #[Test]
    public function type_will_be_registered(): void
    {
        $typeName = $this->getTypeName();

        $this->assertTrue(Type::hasType($typeName), \sprintf('Type %s should be registered', $typeName));

        Type::getType($typeName);
    }

    private function getSelectExpressionForType(string $typeName, string $columnName): string
    {
        // Ensure we get a text representation for PostGIS types that DBAL might map to resource/stream
        return match ($typeName) {
            'geometry' => \sprintf('ST_AsEWKT("%s") AS "%s"', $columnName, $columnName),
            'geography' => \sprintf('ST_AsEWKT("%s"::geometry) AS "%s"', $columnName, $columnName),
            'geometry[]' => \sprintf('ARRAY(SELECT CASE WHEN ST_SRID(geom) = 0 THEN ST_AsText(geom) ELSE \'SRID=\' || ST_SRID(geom) || \';\' || ST_AsText(geom) END FROM unnest("%s") AS geom) AS "%s"', $columnName, $columnName),
            'geography[]' => \sprintf('ARRAY(SELECT CASE WHEN ST_SRID(geog::geometry) = 0 THEN ST_AsText(geog::geometry) ELSE \'SRID=\' || ST_SRID(geog::geometry) || \';\' || ST_AsText(geog::geometry) END FROM unnest("%s") AS geog) AS "%s"', $columnName, $columnName),
            default => $columnName,
        };
    }
}
