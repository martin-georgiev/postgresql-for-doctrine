<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\DataProvider;

class DoublePrecisionArrayTest extends TestCase
{
    protected function getTableName(): string
    {
        return 'test_double_precision_array';
    }

    protected function getColumnName(): string
    {
        return 'double_precision_array_column';
    }

    protected function getColumnType(): string
    {
        return 'DOUBLE PRECISION[]';
    }

    #[DataProvider('provideValidDoublePrecisionArray')]
    public function test_basic_double_precision_array_operations(?string $testData, ?string $expected): void
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

    #[DataProvider('provideInvalidDoublePrecisionArray')]
    public function test_double_precision_array_with_invalid_value(string $invalidData, string $expectedException = Exception::class): void
    {
        $this->expectException($expectedException);
        $this->insertTestData($invalidData);
    }

    public static function provideValidDoublePrecisionArray(): array
    {
        return [
            'basic numbers' => [
                '{1.5,2.5,3.5}',
                '{1.5,2.5,3.5}',
            ],
            'negative numbers' => [
                '{-1.5,-2.5,-3.5}',
                '{-1.5,-2.5,-3.5}',
            ],
            'zero values' => [
                '{0.0,0.0}',
                '{0,0}',
            ],
            'mixed values' => [
                '{1.5,-2.5,0.0}',
                '{1.5,-2.5,0}',
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

    public static function provideInvalidDoublePrecisionArray(): array
    {
        return [
            'invalid value' => [
                '{invalid}',
            ],
            'missing braces' => [
                '1.5,2.5,3.5',
            ],
            'invalid format' => [
                '{1.5 2.5 3.5}',
            ],
            'extra spaces' => [
                '{ 1.5 , 2.5 , 3.5 }',
            ],
            'invalid decimal' => [
                '{1.2.3}',
            ],
        ];
    }
}
