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

    protected function buildTableName(string $columnType): string
    {
        return 'test_type_'.\strtolower(\str_replace([' ', '[]', '()'], ['_', '_array', ''], $columnType));
    }

    /**
     * Prepare a test table for a round trip and return the [tableName, columnName].
     * Caller is responsible to drop the table (typically in a finally block).
     *
     * @return array{string,string}
     */
    protected function prepareTestTable(string $columnType): array
    {
        $tableName = $this->buildTableName($columnType);
        $columnName = 'test_column';
        $this->createTestTableForDataType($tableName, $columnName, $columnType);

        return [$tableName, $columnName];
    }

    /**
     * Read value back from the DB, convert using DBAL type and return it.
     */
    protected function fetchConvertedValue(string $typeName, string $tableName, string $columnName): mixed
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select($this->getSelectExpression($columnName))
            ->from(self::DATABASE_SCHEMA.'.'.$tableName)
            ->where('id = 1');

        $row = $queryBuilder->executeQuery()->fetchAssociative();
        \assert(\is_array($row) && \array_key_exists($columnName, $row));

        $platform = $this->connection->getDatabasePlatform();

        return Type::getType($typeName)->convertToPHPValue($row[$columnName], $platform);
    }

    protected function assertRoundTrip(string $typeName, mixed $expected, mixed $retrieved): void
    {
        if ($expected === null) {
            $this->assertNull($retrieved);

            return;
        }

        $this->assertTypeValueEquals($expected, $retrieved, $typeName);
    }

    /**
     * Perform a round-trip using DBAL parameter binding for insertion.
     */
    protected function runDbalBindingRoundTrip(string $typeName, string $columnType, mixed $value): void
    {
        [$tableName, $columnName] = $this->prepareTestTable($columnType);

        try {
            $qb = $this->connection->createQueryBuilder();
            $qb->insert(self::DATABASE_SCHEMA.'.'.$tableName)
                ->values([$columnName => ':value'])
                ->setParameter('value', $value, $typeName)
                ->executeStatement();

            $retrieved = $this->fetchConvertedValue($typeName, $tableName, $columnName);
            $this->assertRoundTrip($typeName, $value, $retrieved);
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

    protected function getSelectExpression(string $columnName): string
    {
        return $columnName;
    }
}
