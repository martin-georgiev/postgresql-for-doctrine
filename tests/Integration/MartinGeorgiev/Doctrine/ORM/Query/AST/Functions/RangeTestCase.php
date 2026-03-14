<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Tests\Integration\MartinGeorgiev\TestCase as BaseTestCase;

abstract class RangeTestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTableForRangeFixture();
        $this->insertTestDataForRangeFixture();
    }

    protected function createTestTableForRangeFixture(): void
    {
        $tableName = 'containsranges';

        $this->createTestSchema();
        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                int4range INT4RANGE,
                int8range INT8RANGE,
                numrange NUMRANGE,
                daterange DATERANGE,
                tsrange TSRANGE,
                tstzrange TSTZRANGE
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);
    }

    protected function insertTestDataForRangeFixture(): void
    {
        $sql = \sprintf("
            INSERT INTO %s.containsranges (int4range, int8range, numrange, daterange, tsrange, tstzrange) VALUES
            ('[1,10)', '[100,200)', '[1.5,5.5)', '[2023-01-01,2023-06-01)', '[\"2023-01-01 00:00:00\",\"2023-06-01 00:00:00\")', '[\"2023-01-01 00:00:00+00\",\"2023-06-01 00:00:00+00\")'),
            ('[5,15)', '[150,250)', '[3.0,8.0)', '[2023-03-01,2023-09-01)', '[\"2023-03-01 00:00:00\",\"2023-09-01 00:00:00\")', '[\"2023-03-01 00:00:00+00\",\"2023-09-01 00:00:00+00\")')
        ", self::DATABASE_SCHEMA);
        $this->connection->executeStatement($sql);
    }
}
