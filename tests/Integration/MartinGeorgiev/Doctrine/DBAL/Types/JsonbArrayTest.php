<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\DataProvider;

class JsonbArrayTest extends TestCase
{
    protected function getTableName(): string
    {
        return 'test_jsonb_array';
    }

    protected function getColumnName(): string
    {
        return 'jsonb_array_column';
    }

    protected function getColumnType(): string
    {
        return 'JSONB[]';
    }

    #[DataProvider('provideValidJsonbArray')]
    public function test_basic_jsonb_array_operations(?string $testData, ?array $expected): void
    {
        $id = $this->insertTestData($testData);
        $results = $this->getTestData($id);
        $this->assertSame($expected, $results);
    }

    #[DataProvider('provideInvalidJsonbArray')]
    public function test_jsonb_array_with_invalid_value(string $invalidData, string $expectedException = Exception::class): void
    {
        $this->expectException($expectedException);
        $this->insertTestData($invalidData);
    }

    public static function provideValidJsonbArray(): array
    {
        return [
            'basic json objects' => ['{"{\"key1\": \"value1\"}","{\"key2\": \"value2\"}"}', [['key1' => 'value1'], ['key2' => 'value2']]],
            'nested json objects' => ['{"{\"key1\": {\"nested\": \"value\"}}"}', [['key1' => ['nested' => 'value']]]],
            'json arrays' => ['{"[1,2,3]","[4,5,6]"}', [[1, 2, 3], [4, 5, 6]]],
            'mixed types' => ['{"{\"string\": \"value\", \"number\": 42, \"boolean\": true}"}', [['string' => 'value', 'number' => 42, 'boolean' => true]]],
            'null value' => [null, null],
        ];
    }

    public static function provideInvalidJsonbArray(): array
    {
        return [
            'invalid json' => ['{"{\"key\": invalid}"}'],
            'missing braces' => ['"{\"key\": \"value\"}","{\"key2\": \"value2\"}"'],
            'invalid format' => ['{"{\"key\": \"value\"};{\"key2\": \"value2\"}"}'],
            'extra spaces' => ['{ "{\"key\": \"value\"}" , "{\"key2\": \"value2\"}" }'],
        ];
    }
}
