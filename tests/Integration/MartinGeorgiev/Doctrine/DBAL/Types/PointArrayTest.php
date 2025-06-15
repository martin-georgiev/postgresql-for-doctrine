<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\DataProvider;

class PointArrayTest extends TestCase
{
    protected function getTableName(): string
    {
        return 'test_point_array';
    }

    protected function getColumnName(): string
    {
        return 'point_array_column';
    }

    protected function getColumnType(): string
    {
        return 'POINT[]';
    }

    #[DataProvider('provideValidPointArray')]
    public function test_basic_point_array_operations(?string $testData, ?string $expected): void
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

    #[DataProvider('provideInvalidPointArray')]
    public function test_point_array_with_invalid_value(string $invalidData, string $expectedException = Exception::class): void
    {
        $this->expectException($expectedException);
        $this->insertTestData($invalidData);
    }

    public static function provideValidPointArray(): array
    {
        return [
            'basic points' => [
                '{"(1,2)","(3,4)"}',
                '{"(1,2)","(3,4)"}',
            ],
            'negative coordinates' => [
                '{"(-1,-2)","(-3,-4)"}',
                '{"(-1,-2)","(-3,-4)"}',
            ],
            'decimal coordinates' => [
                '{"(1.5,2.5)","(3.5,4.5)"}',
                '{"(1.5,2.5)","(3.5,4.5)"}',
            ],
            'zero coordinates' => [
                '{"(0,0)","(0,0)"}',
                '{"(0,0)","(0,0)"}',
            ],
            'null value' => [
                null,
                null,
            ],
        ];
    }

    public static function provideInvalidPointArray(): array
    {
        return [
            'missing braces' => [
                '(1,2),(3,4)',
            ],
            'invalid format' => [
                '{"(1)","(3,4)"}',
            ],
            'invalid coordinates' => [
                '{"(a,b)","(3,4)"}',
            ],
            'extra spaces' => [
                '{"( 1 , 2 )","(3,4)"}',
            ],
            'invalid characters' => [
                '{"(1,2,3)","(3,4)"}',
            ],
        ];
    }
}
