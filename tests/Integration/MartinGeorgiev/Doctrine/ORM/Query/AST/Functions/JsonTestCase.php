<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Tests\Integration\MartinGeorgiev\TestCase as BaseTestCase;

abstract class JsonTestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createTestTableForJsonFixture();
        $this->insertTestDataForJsonFixture();
    }

    protected function createTestTableForJsonFixture(): void
    {
        $tableName = 'containsjsons';

        $this->createTestSchema();
        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                object1 JSONB,
                object2 JSONB
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);
    }

    protected function insertTestDataForJsonFixture(): void
    {
        $json1 = '{"name": "John", "age": 30, "tags": ["developer", "manager"], "address": {"city": "New York"}}';
        $json2 = '{"name": "Jane", "age": 25, "tags": ["designer"], "address": {"city": "Boston"}}';

        $sql = \sprintf("\n            INSERT INTO %s.containsjsons (object1, object2) VALUES 
            ('%s', '%s'),
            ('%s', '%s')
        ", self::DATABASE_SCHEMA, $json1, $json1, $json2, $json2);
        $this->connection->executeStatement($sql);
    }
}
