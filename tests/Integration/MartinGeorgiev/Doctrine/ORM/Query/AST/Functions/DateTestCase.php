<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Tests\Integration\MartinGeorgiev\TestCase;

abstract class DateTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTableForDateFixture();
        $this->insertTestDataForDateFixture();
    }

    protected function createTestTableForDateFixture(): void
    {
        $tableName = 'containsdates';

        $this->createTestSchema();
        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                date1 DATE,
                date2 DATE,
                datetime1 TIMESTAMP,
                datetime2 TIMESTAMP,
                time1 TIME,
                time2 TIME
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);
    }

    protected function insertTestDataForDateFixture(): void
    {
        $sql = \sprintf('
            INSERT INTO %s.containsdates (date1, date2, datetime1, datetime2, time1, time2) VALUES 
            (\'2023-06-15\', \'2023-06-16\', \'2023-06-15 10:30:00\', \'2023-06-16 11:45:00\', \'10:30:00\', \'11:45:00\')
        ', self::DATABASE_SCHEMA);
        $this->connection->executeStatement($sql);
    }
}
