<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\Test;

final class JsonArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'json[]';
    }

    #[Test]
    public function writes_a_null_item_as_a_sql_null_element(): void
    {
        [$tableName, $columnName] = $this->prepareTestTable($this->getPostgresTypeName());

        try {
            $this->connection->createQueryBuilder()
                ->insert(self::DATABASE_SCHEMA.'.'.$tableName)
                ->values([$columnName => ':value'])
                ->setParameter('value', [null, 1], $this->getTypeName())
                ->executeStatement();

            $storedLiteral = $this->connection->fetchOne(\sprintf(
                'SELECT ("%s")::text FROM %s.%s WHERE id = 1',
                $columnName,
                self::DATABASE_SCHEMA,
                $tableName
            ));
            $this->assertSame('{NULL,1}', $storedLiteral);

            // json_typeof answers 'null' for a JSON null and SQL NULL only for a SQL NULL element - the sole way to tell them apart.
            $firstElementType = $this->connection->fetchOne(\sprintf(
                'SELECT json_typeof(("%s")[1]) FROM %s.%s WHERE id = 1',
                $columnName,
                self::DATABASE_SCHEMA,
                $tableName
            ));
            $this->assertNull($firstElementType);
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }

    /**
     * @return array<string, array{array<int, mixed>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'json array with null items among json values' => [[
                null,
                ['key' => 'value'],
                null,
                1,
            ]],
            'simple json array' => [[
                ['key1' => 'value1', 'key2' => false],
                ['key1' => 'value2', 'key2' => true],
            ]],
            'json array with nested structures' => [[
                [
                    'user' => ['id' => 1, 'name' => 'John'],
                    'meta' => ['active' => true, 'roles' => ['user']],
                ],
                [
                    'user' => ['id' => 2, 'name' => 'Jane'],
                    'meta' => ['active' => false, 'roles' => ['user']],
                ],
            ]],
            'json array with mixed types' => [[
                [
                    'string' => 'value',
                    'number' => 42,
                    'float' => 3.14,
                    'boolean' => false,
                    'null' => null,
                    'array' => [1, 2, 3],
                ],
                [
                    'different' => 'structure',
                    'count' => 999,
                    'enabled' => true,
                ],
            ]],
            'json array with large numeric strings' => [[
                [
                    'bigint' => '9223372036854775807',
                    'regular' => 123,
                ],
                [
                    'huge_number' => '18446744073709551615',
                    'small' => 1,
                ],
            ]],
        ];
    }
}
