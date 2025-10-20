<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

/**
 * Provides ORM-level entity persistence testing for DBAL types.
 *
 * This trait enables testing of convertToPHPValueSQL() by using EntityManager->find()
 * and DQL SELECT queries, which rely on Doctrine ORM automatically calling
 * convertToPHPValueSQL() to wrap columns in SQL expressions.
 *
 * Usage:
 * 1. Implement getEntityClass() to return the entity FQCN
 * 2. Implement getEntityColumnName() to return the column property name
 * 3. Optionally override assertOrmValueEquals() for custom comparison logic
 *
 * Example:
 * ```php
 * final class GeometryTypeTest extends TestCase
 * {
 *     use OrmEntityPersistenceTrait;
 *
 *     protected function getEntityClass(): string
 *     {
 *         return ContainsGeometries::class;
 *     }
 *
 *     protected function getEntityColumnName(): string
 *     {
 *         return 'geometry1';
 *     }
 * }
 * ```
 */
trait OrmEntityPersistenceTrait
{
    /**
     * Returns the fully qualified class name of the entity to use for ORM tests.
     *
     * @return class-string
     */
    abstract protected function getEntityClass(): string;

    /**
     * Returns the property name of the column to test in the entity.
     *
     * @return non-empty-string
     */
    abstract protected function getEntityColumnName(): string;

    /**
     * Returns the table name for the entity (defaults to lowercase entity class name).
     */
    protected function getEntityTableName(): string
    {
        $entityClass = $this->getEntityClass();
        $shortName = \substr((string) $entityClass, \strrpos((string) $entityClass, '\\') + 1);

        return \strtolower($shortName);
    }

    /**
     * Assert that ORM-retrieved value equals expected value.
     */
    protected function assertOrmValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertTypeValueEquals($expected, $actual, $typeName);
    }

    /**
     * Test EntityManager->find() retrieves the value correctly.
     * This verifies that convertToPHPValueSQL() works with ORM's find() method.
     */
    protected function runOrmFindRoundTrip(string $typeName, string $columnType, mixed $value): void
    {
        [$tableName, $columnName] = $this->prepareTestTable($columnType);

        try {
            $qb = $this->connection->createQueryBuilder();
            $qb->insert(self::DATABASE_SCHEMA.'.'.$tableName)
                ->values([$columnName => ':value'])
                ->setParameter('value', $value, $typeName)
                ->executeStatement();

            $entity = $this->entityManager->find($this->getEntityClass(), 1);
            $this->assertNotNull($entity, 'Entity should be found by EntityManager::find()');

            $propertyName = $this->getEntityColumnName();

            if ($value === null) {
                // For NULL values, check if property is initialized
                // Doctrine may not initialize properties for NULL values
                $reflectionProperty = new \ReflectionProperty($entity::class, $propertyName);
                if (!$reflectionProperty->isInitialized($entity)) {
                    // Property not initialized means NULL value - this is expected
                    $this->addToAssertionCount(1);
                } else {
                    $this->assertNull($entity->{$propertyName});
                }
            } else {
                $retrieved = $entity->{$propertyName};
                $this->assertOrmValueEquals($value, $retrieved, $typeName);
            }
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }

    /**
     * Test DQL SELECT query retrieves the value correctly.
     * This verifies that convertToPHPValueSQL() works with DQL SELECT clauses.
     */
    protected function runOrmDqlSelectRoundTrip(string $typeName, string $columnType, mixed $value): void
    {
        [$tableName, $columnName] = $this->prepareTestTable($columnType);

        try {
            $qb = $this->connection->createQueryBuilder();
            $qb->insert(self::DATABASE_SCHEMA.'.'.$tableName)
                ->values([$columnName => ':value'])
                ->setParameter('value', $value, $typeName)
                ->executeStatement();

            $entityClass = $this->getEntityClass();
            $dql = \sprintf('SELECT e FROM %s e WHERE e.id = 1', $entityClass);
            $query = $this->entityManager->createQuery($dql);
            $result = $query->getSingleResult();

            $this->assertNotNull($result, 'Entity should be retrieved by DQL SELECT');
            \assert(\is_object($result));
            $entity = $result;

            $propertyName = $this->getEntityColumnName();

            if ($value === null) {
                // For NULL values, check if property is initialized
                $reflectionProperty = new \ReflectionProperty($entity::class, $propertyName);
                if ($reflectionProperty->isInitialized($entity)) {
                    $this->assertNull($entity->{$propertyName});
                } else {
                    // Property not initialized means NULL value - this is expected
                    $this->addToAssertionCount(1);
                }
            } else {
                $this->assertOrmValueEquals($value, $entity->{$propertyName}, $typeName);
            }
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }

    /**
     * @return array{string, string}
     */
    protected function prepareTestTable(string $columnType): array
    {
        $tableName = $this->getEntityTableName();
        $columnName = $this->getEntityColumnName();
        $this->createTestTableForEntity($tableName);

        return [$tableName, $columnName];
    }

    /**
     * Override this method in your test class to define the complete table schema
     * for your entity. The table must include ALL columns from the entity, not just
     * the one being tested, so Doctrine ORM can properly hydrate the entity.
     *
     * Example:
     * ```php
     * protected function createTestTableForEntity(string $tableName): void
     * {
     *     $this->dropTestTableIfItExists($tableName);
     *     $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
     *     $sql = \sprintf('CREATE TABLE %s (id SERIAL PRIMARY KEY, col1 TYPE1, col2 TYPE2)', $fullTableName);
     *     $this->connection->executeStatement($sql);
     * }
     * ```
     */
    abstract protected function createTestTableForEntity(string $tableName): void;
}
