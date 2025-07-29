<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

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
        $sql = \sprintf('
            INSERT INTO %s.containsranges (
                int4range, 
                int8range, 
                numrange, 
                daterange, 
                tsrange, 
                tstzrange
            ) VALUES 
            (\'[1,10)\', \'[100,1000)\', \'[1.5,10.7)\', \'[2023-01-01,2023-12-31)\', \'[2023-01-01 10:00:00,2023-01-01 18:00:00)\', \'[2023-01-01 10:00:00+00,2023-01-01 18:00:00+00)\'),
            (\'[5,15)\', \'[500,1500)\', \'[5.5,15.7)\', \'[2023-06-01,2023-12-31)\', \'[2023-06-01 10:00:00,2023-06-01 18:00:00)\', \'[2023-06-01 10:00:00+00,2023-06-01 18:00:00+00)\'),
            (\'[20,30)\', \'[2000,3000)\', \'[20.5,30.7)\', \'[2023-12-01,2023-12-31)\', \'[2023-12-01 10:00:00,2023-12-01 18:00:00)\', \'[2023-12-01 10:00:00+00,2023-12-01 18:00:00+00)\')
        ', self::DATABASE_SCHEMA);
        $this->connection->executeStatement($sql);
    }
}
