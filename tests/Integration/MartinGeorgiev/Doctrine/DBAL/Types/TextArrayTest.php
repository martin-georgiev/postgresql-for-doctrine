<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TextArrayTest extends TestCase
{
    protected function getTableName(): string
    {
        return 'test_text_array';
    }

    protected function getColumnName(): string
    {
        return 'text_array_column';
    }

    protected function getColumnType(): string
    {
        return 'TEXT[]';
    }

    #[DataProvider('provideValidTextArray')]
    public function test_basic_text_array_operations(?string $testData, ?array $expected): void
    {
        $id = $this->insertTestData($testData);
        $results = $this->getTestData($id);

        if ($expected === null) {
            $this->assertNull($results);
        } else {
            $this->assertIsArray($results);
            $this->assertEquals($expected, $results);
        }
    }

    #[DataProvider('provideInvalidTextArray')]
    public function test_text_array_with_invalid_value(string $invalidData, string $expectedException = Exception::class): void
    {
        $this->expectException($expectedException);
        $this->insertTestData($invalidData);
    }

    public static function provideValidTextArray(): array
    {
        return [
            'basic strings' => ['{"apple","banana","orange"}', ['apple', 'banana', 'orange']],
            'empty strings' => ['{"","",""}', ['', '', '']],
            'special characters' => ['{"hello,world","test;string","quoted""string"}', ['hello,world', 'test;string', 'quoted"string']],
            'quoted strings' => ['{"hello world","test string"}', ['hello world', 'test string']],
            'null value' => [null, null],
        ];
    }

    public static function provideInvalidTextArray(): array
    {
        return [
            'invalid characters' => ['{"invalid\0character"}'],
            'missing quotes' => ['{unquoted,string}'],
            'invalid format' => ['{text;with;semicolons}'],
            'extra spaces' => ['{ text , with , spaces }'],
        ];
    }
}
