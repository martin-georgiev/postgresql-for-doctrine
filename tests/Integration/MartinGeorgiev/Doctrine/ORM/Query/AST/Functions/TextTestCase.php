<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Tests\Integration\MartinGeorgiev\TestCase;

abstract class TextTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTableForTextFixture();
        $this->insertTestDataForTextFixture();
    }

    protected function createTestTableForTextFixture(): void
    {
        $tableName = 'containstexts';

        $this->createTestSchema();
        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                text1 TEXT,
                text2 TEXT
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);
    }

    protected function insertTestDataForTextFixture(): void
    {
        $sql = \sprintf('
            INSERT INTO %s.containstexts (text1, text2) VALUES 
            (\'this is a test string\', \'another test string\')
        ', self::DATABASE_SCHEMA);
        $this->connection->executeStatement($sql);
    }
}
