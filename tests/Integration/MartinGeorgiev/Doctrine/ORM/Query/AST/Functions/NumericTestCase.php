<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Tests\Integration\MartinGeorgiev\TestCase;

abstract class NumericTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTableForNumericFixture();
        $this->insertTestDataForNumericFixture();
    }

    protected function createTestTableForNumericFixture(): void
    {
        $tableName = 'containsnumerics';

        $this->createTestSchema();
        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                integer1 INTEGER,
                integer2 INTEGER,
                bigint1 BIGINT,
                bigint2 BIGINT,
                decimal1 DECIMAL,
                decimal2 DECIMAL
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);
    }

    protected function insertTestDataForNumericFixture(): void
    {
        $sql = \sprintf('
            INSERT INTO %s.containsnumerics (integer1, integer2, bigint1, bigint2, decimal1, decimal2) VALUES 
            (10, 20, 1000, 2000, 10.5, 20.5)
        ', self::DATABASE_SCHEMA);
        $this->connection->executeStatement($sql);
    }
}
