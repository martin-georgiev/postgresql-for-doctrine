<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MacaddrTest extends TestCase
{
    protected function getTableName(): string
    {
        return 'test_macaddr';
    }

    protected function getColumnName(): string
    {
        return 'macaddr_column';
    }

    protected function getColumnType(): string
    {
        return 'MACADDR';
    }

    #[DataProvider('provideValidMacaddr')]
    public function test_basic_macaddr_operations(?string $testData, ?string $expected): void
    {
        $id = $this->insertTestData($testData);
        $results = $this->getTestData($id);

        if ($expected === null) {
            $this->assertNull($results);
        } else {
            $this->assertIsString($results);
            $this->assertEquals($expected, $results);
        }
    }

    #[DataProvider('provideInvalidMacaddr')]
    public function test_macaddr_with_invalid_value(string $invalidData, string $expectedException = Exception::class): void
    {
        $this->expectException($expectedException);
        $this->insertTestData($invalidData);
    }

    public static function provideValidMacaddr(): array
    {
        return [
            'colon format' => ['08:00:2b:01:02:03', '08:00:2b:01:02:03'],
            'dash format' => ['08-00-2b-01-02-03', '08:00:2b:01:02:03'],
            'dot format' => ['0800.2b01.0203', '08:00:2b:01:02:03'],
            'null value' => [null, null],
        ];
    }

    public static function provideInvalidMacaddr(): array
    {
        return [
            'mixed separators' => ['08:00-2b:01.02:03'],
            'invalid format' => ['invalid'],
            'missing separators' => ['08002b010203'],
            'extra spaces' => [' 08:00:2b:01:02:03 '],
            'invalid characters' => ['08:00:2b:01:02:0g'],
        ];
    }
}
