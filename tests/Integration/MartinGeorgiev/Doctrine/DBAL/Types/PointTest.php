<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\DataProvider;

class PointTest extends TestCase
{
    protected function getTableName(): string
    {
        return 'test_point';
    }

    protected function getColumnName(): string
    {
        return 'point_column';
    }

    protected function getColumnType(): string
    {
        return 'POINT';
    }

    #[DataProvider('provideValidPoint')]
    public function test_basic_point_operations(?string $testData, ?string $expected): void
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

    #[DataProvider('provideInvalidPoint')]
    public function test_point_with_invalid_value(string $invalidData, string $expectedException = Exception::class): void
    {
        $this->expectException($expectedException);
        $this->insertTestData($invalidData);
    }

    public static function provideValidPoint(): array
    {
        return [
            'basic point' => [
                '(1,2)',
                '(1,2)',
            ],
            'negative coordinates' => [
                '(-1,-2)',
                '(-1,-2)',
            ],
            'decimal coordinates' => [
                '(1.5,2.5)',
                '(1.5,2.5)',
            ],
            'zero coordinates' => [
                '(0,0)',
                '(0,0)',
            ],
            'null value' => [
                null,
                null,
            ],
        ];
    }

    public static function provideInvalidPoint(): array
    {
        return [
            'missing parentheses' => [
                '1,2',
            ],
            'invalid format' => [
                '(1)',
            ],
            'invalid coordinates' => [
                '(a,b)',
            ],
            'extra spaces' => [
                '( 1 , 2 )',
            ],
            'invalid characters' => [
                '(1,2,3)',
            ],
        ];
    }
}
