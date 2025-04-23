<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray;

class IntegerArrayTest extends BaseIntegerArrayTestCase
{
    protected function setUp(): void
    {
        $this->fixture = new IntegerArray();
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        self::assertEquals('integer[]', $this->fixture->getName());
    }

    public static function provideInvalidPHPValuesForDatabaseTransformation(): array
    {
        return \array_merge(parent::provideInvalidPHPValuesForDatabaseTransformation(), [
            ['2147483648'],   // Greater than max integer
            ['-2147483649'],  // Less than min integer
            ['1.23e6'],      // Scientific notation
            ['123456.789'],   // Decimal number
        ]);
    }

    /**
     * @return array<int, array{phpValue: int, postgresValue: string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            [
                'phpValue' => 2147483647,
                'postgresValue' => '2147483647',
            ],
            [
                'phpValue' => -2147483648,
                'postgresValue' => '-2147483648',
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
                'phpValue' => 999999999,
                'postgresValue' => '999999999',
            ],
        ];
    }

    /**
     * @return array<array{string}>
     */
    public static function provideOutOfRangeValues(): array
    {
        return [
            ['2147483648'], // MAX_INTEGER + 1
            ['-2147483649'], // MIN_INTEGER - 1
        ];
    }
}
