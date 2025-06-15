<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\DataProvider;

class BooleanArrayTest extends TestCase
{
    protected function getTableName(): string
    {
        return 'test_boolean_array';
    }

    protected function getColumnName(): string
    {
        return 'boolean_array_column';
    }

    protected function getColumnType(): string
    {
        return 'BOOLEAN[]';
    }

    #[DataProvider('provideValidBooleanArray')]
    public function test_basic_boolean_array_operations(?string $testData, array $expected): void
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

    #[DataProvider('provideInvalidBooleanArray')]
    public function test_boolean_array_with_invalid_value(string $invalidData, string $expectedException = Exception::class): void
    {
        $this->expectException($expectedException);
        $this->insertTestData($invalidData);
    }

    public static function provideValidBooleanArray(): array
    {
        return [
            'basic booleans' => ['{true,false}', [true, false]],
            'single true' => ['{true}', [true]],
            'single false' => ['{false}', [false]],
            'mixed values' => ['{true,false,true}', [true, false, true]],
            'null value' => [null, null],
        ];
    }

    public static function provideInvalidBooleanArray(): array
    {
        return [
            'invalid values' => ['{true,invalid}'],
            'missing braces' => ['true,false'],
            'invalid format' => ['{true;false}'],
            'extra spaces' => ['{ true , false }'],
        ];
    }
}
