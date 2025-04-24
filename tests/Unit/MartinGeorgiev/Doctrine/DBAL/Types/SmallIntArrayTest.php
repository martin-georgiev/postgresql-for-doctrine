<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray;

class SmallIntArrayTest extends BaseIntegerArrayTestCase
{
    protected function setUp(): void
    {
        $this->fixture = new SmallIntArray();
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        self::assertEquals('smallint[]', $this->fixture->getName());
    }

    public static function provideInvalidPHPValuesForDatabaseTransformation(): array
    {
        return \array_merge(parent::provideInvalidPHPValuesForDatabaseTransformation(), [
            ['32768'],    // Greater than max smallint
            ['-32769'],   // Less than min smallint
            ['1.23e4'],   // Scientific notation
            ['123.45'],   // Decimal number
        ]);
    }

    /**
     * @return array<int, array{phpValue: int, postgresValue: string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            [
                'phpValue' => 32767,
                'postgresValue' => '32767',
            ],
            [
                'phpValue' => -32768,
                'postgresValue' => '-32768',
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
                'phpValue' => 9999,
                'postgresValue' => '9999',
            ],
        ];
    }

    /**
     * @return array<array{string}>
     */
    public static function provideOutOfRangeValues(): array
    {
        return [
            ['32768'], // MAX_SMALLINT + 1
            ['-32769'], // MIN_SMALLINT - 1
        ];
    }
}
