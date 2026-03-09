<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Vector;

use Tests\Integration\MartinGeorgiev\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureVectorExtension();
        $this->createTestTableForVectorFixture();
        $this->insertTestDataForVectorFixture();
    }

    private function ensureVectorExtension(): void
    {
        try {
            $this->connection->executeStatement('CREATE EXTENSION IF NOT EXISTS vector');
        } catch (\Exception) {
            $this->markTestSkipped('pgvector extension is not available');
        }
    }

    protected function createTestTableForVectorFixture(): void
    {
        $tableName = 'containsvectors';

        $this->createTestSchema();
        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                vector1 vector(3),
                vector2 vector(3)
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);
    }

    protected function insertTestDataForVectorFixture(): void
    {
        $sql = \sprintf('
            INSERT INTO %s.containsvectors (vector1, vector2) VALUES
            (\'[1,2,3]\', \'[1,2,3]\'),
            (\'[1,0,0]\', \'[0,1,0]\'),
            (\'[4,5,6]\', \'[1,2,3]\')
        ', self::DATABASE_SCHEMA);
        $this->connection->executeStatement($sql);
    }
}
