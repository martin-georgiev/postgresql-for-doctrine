<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Tests\Integration\MartinGeorgiev\TestCase as BaseTestCase;

abstract class ArrayTestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createTestTableForArrayFixture();
        $this->insertTestDataForArrayFixture();
    }

    protected function createTestTableForArrayFixture(): void
    {
        $tableName = 'containsarrays';

        $this->createTestSchema();
        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                textarray TEXT[],
                smallintarray SMALLINT[],
                integerarray INTEGER[],
                bigintarray BIGINT[],
                boolarray BOOLEAN[]
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);
    }

    protected function insertTestDataForArrayFixture(): void
    {
        $sql = \sprintf('
            INSERT INTO %s.containsarrays (textarray, smallintarray, integerarray, bigintarray, boolarray) VALUES
            (ARRAY[\'apple\', \'banana\', \'orange\'], ARRAY[1, 2, 3],  ARRAY[1, 2, 3],  ARRAY[1, 2, 3], ARRAY[true, false, true]),
            (ARRAY[\'grape\', \'apple\'], ARRAY[4, 1], ARRAY[4, 1], ARRAY[4, 1], ARRAY[false, true]),
            (ARRAY[\'banana\', \'orange\', \'kiwi\', \'mango\'], ARRAY[2, 3, 7, 8], ARRAY[2, 3, 7, 8], ARRAY[2, 3, 7, 8], ARRAY[true, true, false, true])
        ', self::DATABASE_SCHEMA);
        $this->connection->executeStatement($sql);
    }
}
