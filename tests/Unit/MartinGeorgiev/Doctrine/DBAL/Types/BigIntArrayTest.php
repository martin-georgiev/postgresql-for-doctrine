<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidIntegerArrayItemForPHPException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class BigIntArrayTest extends BaseIntegerArrayTestCase
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
     * @return list<array{
     *     phpValue: int,
     *     postgresValue: string
     * }>
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

    #[DataProvider('provideOutOfRangeValues')]
    #[Test]
    public function throws_domain_exception_when_value_exceeds_range(string $outOfRangeValue): void
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
