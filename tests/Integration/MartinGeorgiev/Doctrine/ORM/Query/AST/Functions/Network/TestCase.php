<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use Tests\Integration\MartinGeorgiev\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTableForNetworkFixture();
        $this->insertTestDataForNetworkFixture();
    }

    protected function createTestTableForNetworkFixture(): void
    {
        $tableName = 'containsnetworks';

        $this->createTestSchema();
        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                ip INET,
                cidr CIDR
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);
    }

    protected function insertTestDataForNetworkFixture(): void
    {
        $sql = \sprintf("
            INSERT INTO %s.containsnetworks (ip, cidr) VALUES
            ('192.168.1.5/24', '192.168.1.0/24'),
            ('10.0.0.1/8', '10.0.0.0/8')
        ", self::DATABASE_SCHEMA);
        $this->connection->executeStatement($sql);
    }
}
