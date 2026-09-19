<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntegerArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class BigIntArrayTest extends BaseIntegerArrayTestCase
{
    protected function setUp(): void
    {
        $this->fixture = new BigIntArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('bigint[]', $this->fixture->getName());
    }

    public static function provideInvalidDatabaseValueInputs(): array
    {
        return \array_merge(parent::provideInvalidDatabaseValueInputs(), [
            'greater than PHP_INT_MAX' => ['9223372036854775808'],
            'less than PHP_INT_MIN' => ['-9223372036854775809'],
            'scientific notation' => ['1.23e10'],
            'decimal number' => ['12345.67890'],
        ]);
    }

    /**
     * @return array<string, array{postgresValue: string, expectedValue: int}>
     */
    public static function provideValidItemTransformationsToPHP(): array
    {
        return [
            'the upper bound' => [
                'postgresValue' => (string) PHP_INT_MAX,
                'expectedValue' => PHP_INT_MAX,
            ],
            'the lower bound' => [
                'postgresValue' => (string) PHP_INT_MIN,
                'expectedValue' => PHP_INT_MIN,
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
        ];
    }

    #[DataProvider('provideOutOfRangeValues')]
    #[Test]
    public function throws_exception_for_value_exceeding_range(string $outOfRangeValue): void
    {
        $this->expectException(InvalidIntegerArrayItemForPHPException::class);
        $this->expectExceptionMessage('is out of range for PHP integer but appears valid for PostgreSQL');

        $this->fixture->transformArrayItemForPHP($outOfRangeValue);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideOutOfRangeValues(): array
    {
        return [
            'above PHP_INT_MAX' => ['9223372036854775808'],
            'below PHP_INT_MIN' => ['-9223372036854775809'],
        ];
    }
}
