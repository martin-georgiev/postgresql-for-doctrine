<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

abstract class SpatialArrayTypeTestCase extends TestCase
{
    /**
     * Insert an array of spatial WKTs using ARRAY[...] with per-element casts, then retrieve and assert.
     *
     * @param non-empty-string $elementPgType 'geometry' or 'geography'
     */
    protected function runArrayConstructorTypeTest(string $typeName, string $columnType, string $elementPgType, WktSpatialData ...$spatialData): void
    {
        $tableName = 'test_type_'.\strtolower(\str_replace([' ', '[]', '()'], ['_', '_array', ''], $columnType)).'_ctor';
        $columnName = 'test_column';

        try {
            $this->createTestTableForDataType($tableName, $columnName, $columnType);

            $placeholders = \implode(',', \array_fill(0, \count($spatialData), '?::'.$elementPgType));
            $sql = \sprintf(
                'INSERT INTO %s.%s ("%s") VALUES (ARRAY[%s])',
                self::DATABASE_SCHEMA,
                $tableName,
                $columnName,
                $placeholders
            );

            $stringifiedWkts = \array_map(static fn (WktSpatialData $wktSpatialData): string => (string) $wktSpatialData, $spatialData);
            $this->connection->executeStatement($sql, \array_values($stringifiedWkts));

            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder
                ->select($this->getSelectExpression($columnName))
                ->from(self::DATABASE_SCHEMA.'.'.$tableName)
                ->where('id = 1');

            $row = $queryBuilder->executeQuery()->fetchAssociative();
            \assert(\is_array($row) && \array_key_exists($columnName, $row));

            // Get the value with the correct type
            $platform = $this->connection->getDatabasePlatform();
            $retrievedValue = Type::getType($typeName)->convertToPHPValue($row[$columnName], $platform);

            $this->assertTypeValueEquals($spatialData, $retrievedValue, $typeName);
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }
}
