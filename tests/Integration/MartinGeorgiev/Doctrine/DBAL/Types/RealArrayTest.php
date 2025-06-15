<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\DataProvider;

class RealArrayTest extends TestCase
{
    protected function getTableName(): string
    {
        return 'test_real_array';
    }

    protected function getColumnName(): string
    {
        return 'real_array_column';
    }

    protected function getColumnType(): string
    {
        return 'REAL[]';
    }

    #[DataProvider('provideValidRealArray')]
    public function test_basic_real_array_operations(?string $testData, ?string $expected): void
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

    #[DataProvider('provideInvalidRealArray')]
    public function test_real_array_with_invalid_value(string $invalidData, string $expectedException = Exception::class): void
    {
        $this->expectException($expectedException);
        $this->insertTestData($invalidData);
    }

    public static function provideValidRealArray(): array
    {
        return [
            'basic reals' => ['{1.5,2.5,3.5}', '{1.5,2.5,3.5}'],
            'negative reals' => ['{-1.5,-2.5,-3.5}', '{-1.5,-2.5,-3.5}'],
            'zero values' => ['{0.0,0.0}', '{0,0}'],
            'mixed values' => ['{1.5,-2.5,0.0}', '{1.5,-2.5,0}'],
            'null value' => [null, null],
        ];
    }

    public static function provideInvalidRealArray(): array
    {
        return [
            'invalid values' => ['{1.5,invalid,3.5}'],
            'missing braces' => ['1.5,2.5,3.5'],
            'invalid format' => ['{1.5;2.5;3.5}'],
            'extra spaces' => ['{ 1.5 , 2.5 , 3.5 }'],
        ];
    }
}
