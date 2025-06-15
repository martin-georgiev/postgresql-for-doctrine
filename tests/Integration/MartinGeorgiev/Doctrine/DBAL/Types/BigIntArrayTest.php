<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\DataProvider;

class BigIntArrayTest extends TestCase
{
    protected function getTableName(): string
    {
        return 'test_bigint_array';
    }

    protected function getColumnName(): string
    {
        return 'bigint_array_column';
    }

    protected function getColumnType(): string
    {
        return 'BIGINT[]';
    }

    #[DataProvider('provideValidBigIntArray')]
    public function test_basic_big_int_array_operations(?string $testData, ?string $expected): void
    {
        $id = $this->insertTestData($testData);
        $results = $this->getTestData($id);

        $this->assertCount(1, $results);
        if ($expected === null) {
            $this->assertNull($results[0]);
        } else {
            $this->assertIsString($results[0]);
            $this->assertEquals($expected, $results[0]);
        }
    }

    #[DataProvider('provideInvalidBigIntArray')]
    public function test_big_int_array_with_invalid_value(string $invalidData, string $expectedException = Exception::class): void
    {
        $this->expectException($expectedException);
        $this->insertTestData($invalidData);
    }

    public static function provideValidBigIntArray(): array
    {
        return [
            'basic integers' => [
                '{1,2,3}',
                '{1,2,3}',
            ],
            'negative integers' => [
                '{-1,-2,-3}',
                '{-1,-2,-3}',
            ],
            'large integers' => [
                '{9223372036854775807,-9223372036854775808}',
                '{9223372036854775807,-9223372036854775808}',
            ],
            'mixed values' => [
                '{1,-2,3}',
                '{1,-2,3}',
            ],
            'empty array' => [
                '{}',
                '{}',
            ],
            'null value' => [
                null,
                null,
            ],
        ];
    }

    public static function provideInvalidBigIntArray(): array
    {
        return [
            'invalid value' => [
                '{invalid}',
            ],
            'out of range value' => [
                '{9223372036854775808}',
            ],
            'missing braces' => [
                '1,2,3',
            ],
            'invalid format' => [
                '{1 2 3}',
            ],
            'extra spaces' => [
                '{ 1 , 2 , 3 }',
            ],
        ];
    }
}
