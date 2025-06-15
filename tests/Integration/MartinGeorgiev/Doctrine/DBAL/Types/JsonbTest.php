<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class JsonbTest extends TestCase
{
    protected function getTableName(): string
    {
        return 'test_jsonb';
    }

    protected function getColumnName(): string
    {
        return 'jsonb_column';
    }

    protected function getColumnType(): string
    {
        return 'JSONB';
    }

    #[DataProvider('provideValidJsonb')]
    public function test_basic_jsonb_operations(?string $testData, ?string $expected): void
    {
        $id = $this->insertTestData($testData);
        $results = $this->getTestData($id);

        if ($expected === null) {
            $this->assertNull($results);
        } else {
            $this->assertIsString($results);
            $this->assertEquals(\json_decode($expected, true), \json_decode($results, true));
        }
    }

    #[DataProvider('provideInvalidJsonb')]
    public function test_jsonb_with_invalid_value(string $invalidData, string $expectedException = Exception::class): void
    {
        $this->expectException($expectedException);
        $this->insertTestData($invalidData);
    }

    public static function provideValidJsonb(): array
    {
        return [
            'basic object' => ['{"name": "John", "age": 30}', '{"name": "John", "age": 30}'],
            'nested object' => ['{"user": {"name": "John", "age": 30}}', '{"user": {"name": "John", "age": 30}}'],
            'array' => ['[1, 2, 3]', '[1, 2, 3]'],
            'mixed types' => ['{"numbers": [1, 2, 3], "text": "hello"}', '{"numbers": [1, 2, 3], "text": "hello"}'],
            'null value' => [null, null],
        ];
    }

    public static function provideInvalidJsonb(): array
    {
        return [
            'invalid json' => ['{invalid json}'],
            'missing quotes' => ['{name: "John"}'],
            'extra spaces' => ['{ "name" : "John" }'],
        ];
    }
}
