<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\DataProvider;

class MacaddrArrayTest extends TestCase
{
    protected function getTableName(): string
    {
        return 'test_macaddr_array';
    }

    protected function getColumnName(): string
    {
        return 'macaddr_array_column';
    }

    protected function getColumnType(): string
    {
        return 'MACADDR[]';
    }

    #[DataProvider('provideValidMacaddrArray')]
    public function test_basic_macaddr_array_operations(?string $testData, ?array $expected): void
    {
        $id = $this->insertTestData($testData);
        $results = $this->getTestData($id);
        $this->assertSame($expected, $results);
    }

    #[DataProvider('provideInvalidMacaddrArray')]
    public function test_macaddr_array_with_invalid_value(string $invalidData, string $expectedException = Exception::class): void
    {
        $this->expectException($expectedException);
        $this->insertTestData($invalidData);
    }

    public static function provideValidMacaddrArray(): array
    {
        return [
            'basic mac addresses' => ['{08:00:2b:01:02:03,08:00:2b:01:02:04}', ['08:00:2b:01:02:03', '08:00:2b:01:02:04']],
            'single mac address' => ['{08:00:2b:01:02:03}', ['08:00:2b:01:02:03']],
            'mixed case' => ['{08:00:2B:01:02:03,08:00:2b:01:02:04}', ['08:00:2b:01:02:03', '08:00:2b:01:02:04']],
            'null value' => [null, null],
        ];
    }

    public static function provideInvalidMacaddrArray(): array
    {
        return [
            'invalid values' => ['{08:00:2b:01:02:03,invalid}'],
            'missing braces' => ['08:00:2b:01:02:03,08:00:2b:01:02:04'],
            'invalid format' => ['{08:00:2b:01:02:03;08:00:2b:01:02:04}'],
            'extra spaces' => ['{ 08:00:2b:01:02:03 , 08:00:2b:01:02:04 }'],
        ];
    }
}
