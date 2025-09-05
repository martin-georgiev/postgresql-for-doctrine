<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use Tests\Integration\MartinGeorgiev\TestCase;

abstract class LtreeTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTableForLtreeFixture();
        $this->insertTestDataForLtreeFixture();
    }

    protected function createTestTableForLtreeFixture(): void
    {
        $tableName = 'containsltrees';

        $this->createTestSchema();
        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                ltree1 LTREE,
                ltree2 LTREE,
                ltree3 LTREE
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);
    }

    protected function insertTestDataForLtreeFixture(): void
    {
        $sql = \sprintf('
            INSERT INTO %s.containsltrees (ltree1, ltree2, ltree3) VALUES 
            (\'Top.Child1.Child2\', \'Top.Child1\', \'Top.Child2.Child3\'),
            (\'A.B.C.D\', \'A.B\', \'A.B.C\'),
            (\'Root\', \'Root.Leaf\', \'Root.Branch\')
        ', self::DATABASE_SCHEMA);
        $this->connection->executeStatement($sql);
    }
}
