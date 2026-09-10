<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

abstract class FloatArrayTypeTestCase extends ArrayTypeTestCase
{
    /**
     * @param array<int, float> $expected
     */
    #[DataProvider('providePostgresWrittenValues')]
    #[Test]
    public function converts_postgres_written_value_to_php_value(string $literal, array $expected): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();
        [$tableName, $columnName] = $this->prepareTestTable($columnType);

        try {
            $sql = \sprintf(
                'INSERT INTO %s.%s ("%s") VALUES (?::%s)',
                self::DATABASE_SCHEMA,
                $tableName,
                $columnName,
                $columnType
            );
            $this->connection->executeStatement($sql, [$literal]);

            $retrieved = $this->fetchConvertedValue($typeName, $tableName, $columnName);
            $this->assertTypeValueEquals($expected, $retrieved, $typeName);
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }

    /**
     * @return array<string, array{literal: string, expected: array<int, float>}>
     */
    abstract public static function providePostgresWrittenValues(): array;
}
