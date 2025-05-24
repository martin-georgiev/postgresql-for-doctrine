<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Tests\Integration\MartinGeorgiev\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function createTestTableForDataType(string $tableName, string $columnName, string $columnType): void
    {
        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
        $sql = \sprintf(
            'CREATE TABLE %s (id SERIAL PRIMARY KEY, "%s" %s)',
            $fullTableName,
            $columnName,
            $columnType
        );

        $this->connection->executeStatement($sql);
    }
}
