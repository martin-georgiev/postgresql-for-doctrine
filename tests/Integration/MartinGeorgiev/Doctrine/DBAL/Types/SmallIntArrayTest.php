<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\DataProvider;

class SmallIntArrayTest extends TestCase
{
    protected function getTableName(): string
    {
        return 'test_smallint_array';
    }

    protected function getColumnName(): string
    {
        return 'smallint_array_column';
    }

    protected function getColumnType(): string
    {
        return 'SMALLINT[]';
    }

    #[DataProvider('provideValidSmallIntArray')]
    public function test_basic_smallint_array_operations(?string $testData, ?array $expected): void
    {
        $id = $this->insertTestData($testData);
        $results = $this->getTestData($id);
        $this->assertSame($expected, $results);
    }

    #[DataProvider('provideInvalidSmallIntArray')]
    public function test_smallint_array_with_invalid_value(string $invalidData, string $expectedException = Exception::class): void
    {
        $this->expectException($expectedException);
        $this->insertTestData($invalidData);
    }

    public static function provideValidSmallIntArray(): array
    {
        return [
            'basic smallints' => ['{1,2,3,4,5}', [1, 2, 3, 4, 5]],
            'negative smallints' => ['{-1,-2,-3}', [-1, -2, -3]],
            'zero values' => ['{0,0,0}', [0, 0, 0]],
            'mixed values' => ['{1,-2,0,3}', [1, -2, 0, 3]],
            'null value' => [null, null],
        ];
    }

    public static function provideInvalidSmallIntArray(): array
    {
        return [
            'invalid values' => ['{1,invalid,3}'],
            'missing braces' => ['1,2,3'],
            'invalid format' => ['{1;2;3}'],
            'extra spaces' => ['{ 1 , 2 , 3 }'],
        ];
    }
}
