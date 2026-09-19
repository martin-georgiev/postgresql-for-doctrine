<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray;
use PHPUnit\Framework\Attributes\Test;

final class IntegerArrayTest extends BaseIntegerArrayTestCase
{
    protected function setUp(): void
    {
        $this->fixture = new IntegerArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('integer[]', $this->fixture->getName());
    }

    public static function provideInvalidDatabaseValueInputs(): array
    {
        return \array_merge(parent::provideInvalidDatabaseValueInputs(), [
            'greater than max integer' => ['2147483648'],
            'less than min integer' => ['-2147483649'],
            'scientific notation' => ['1.23e6'],
            'decimal number' => ['123456.789'],
        ]);
    }

    /**
     * @return array<string, array{postgresValue: string, expectedValue: int}>
     */
    public static function provideValidItemTransformationsToPHP(): array
    {
        return [
            'the upper bound' => [
                'postgresValue' => '2147483647',
                'expectedValue' => 2147483647,
            ],
            'the lower bound' => [
                'postgresValue' => '-2147483648',
                'expectedValue' => -2147483648,
            ],
            'zero' => [
                'postgresValue' => '0',
                'expectedValue' => 0,
            ],
            'one' => [
                'postgresValue' => '1',
                'expectedValue' => 1,
            ],
            'minus one' => [
                'postgresValue' => '-1',
                'expectedValue' => -1,
            ],
            'nine digits' => [
                'postgresValue' => '999999999',
                'expectedValue' => 999999999,
            ],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideOutOfRangeValues(): array
    {
        return [
            'above max integer' => ['2147483648'],
            'below min integer' => ['-2147483649'],
        ];
    }
}
