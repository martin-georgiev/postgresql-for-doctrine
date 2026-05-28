<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore;

use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;
use Tests\Integration\MartinGeorgiev\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createTestTableForHstoreFixture();
        $this->insertTestDataForHstoreFixture();
    }

    protected function transformPostgresArray(mixed $value): mixed
    {
        try {
            \assert(\is_string($value));
            $preserveStringTypes = true; // hstore values are always text — preserve string types for numeric-looking values

            return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($value, $preserveStringTypes);
        } catch (\Throwable) {
            return $value;
        }
    }

    private function createTestTableForHstoreFixture(): void
    {
        $tableName = 'containshstores';

        $this->createTestSchema();
        $this->ensurePostgresExtensionInSchema('hstore');
        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                data HSTORE
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);
    }

    private function insertTestDataForHstoreFixture(): void
    {
        $sql = \sprintf("
            INSERT INTO %s.containshstores (data) VALUES
            ('\"a\"=>\"1\",\"b\"=>\"2\",\"c\"=>\"3\"'),
            ('\"x\"=>\"10\",\"y\"=>NULL'),
            ('\"key1\"=>\"value1\",\"key2\"=>\"value2\"')
        ", self::DATABASE_SCHEMA);
        $this->connection->executeStatement($sql);
    }
}
