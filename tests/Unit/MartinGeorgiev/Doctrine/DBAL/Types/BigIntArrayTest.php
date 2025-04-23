<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntegerArrayItemForPHPException;

class BigIntArrayTest extends BaseIntegerArrayTestCase
{
    protected function setUp(): void
    {
        $this->fixture = new BigIntArray();
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        self::assertEquals('bigint[]', $this->fixture->getName());
    }

    public static function provideInvalidPHPValuesForDatabaseTransformation(): array
    {
        return \array_merge(parent::provideInvalidPHPValuesForDatabaseTransformation(), [
            ['9223372036854775808'],    // Greater than PHP_INT_MAX
            ['-9223372036854775809'],   // Less than PHP_INT_MIN
            ['1.23e10'],                // Scientific notation
            ['12345.67890'],            // Decimal number
        ]);
    }

    /**
     * @return array<int, array{phpValue: int, postgresValue: string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            [
                'phpValue' => PHP_INT_MAX,
                'postgresValue' => (string) PHP_INT_MAX,
            ],
            [
                'phpValue' => PHP_INT_MIN,
                'postgresValue' => (string) PHP_INT_MIN,
            ],
            [
                'phpValue' => 0,
                'postgresValue' => '0',
            ],
            [
                'phpValue' => 1,
                'postgresValue' => '1',
            ],
            [
                'phpValue' => -1,
                'postgresValue' => '-1',
            ],
            [
                'phpValue' => 9_223_372_036_854_775_807,
                'postgresValue' => '9223372036854775807',
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideOutOfRangeValues
     */
    public function throws_domain_exception_when_value_exceeds_range(string $outOfRangeValue): void
    {
        $this->expectException(InvalidIntegerArrayItemForPHPException::class);
        $this->expectExceptionMessage('is out of range for PHP integer but appears valid for PostgreSQL');

        $this->fixture->transformArrayItemForPHP($outOfRangeValue);
    }

    /**
     * @return array<array{string}>
     */
    public static function provideOutOfRangeValues(): array
    {
        return [
            ['9223372036854775808'], // PHP_INT_MAX + 1
            ['-9223372036854775809'], // PHP_INT_MIN - 1
        ];
    }
}
