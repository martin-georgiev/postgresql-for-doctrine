<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Tests\Integration\MartinGeorgiev\TestCase;

abstract class BooleanTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTableForBooleanFixture();
        $this->insertTestDataForBooleanFixture();
    }

    protected function createTestTableForBooleanFixture(): void
    {
        $tableName = 'containsbooleans';

        $this->createTestSchema();
        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                bool1 BOOLEAN,
                bool2 BOOLEAN
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);
    }

    protected function insertTestDataForBooleanFixture(): void
    {
        $sql = \sprintf('
            INSERT INTO %s.containsbooleans (bool1, bool2) VALUES
            (TRUE, FALSE),
            (TRUE, TRUE)
        ', self::DATABASE_SCHEMA);
        $this->connection->executeStatement($sql);
    }
}
