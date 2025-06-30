<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NumericRangeTest extends TestCase
{
    #[Test]
    public function can_create_simple_range(): void
    {
        $numericRange = new NumericRange(1.5, 10.7);

        self::assertEquals('[1.5,10.7)', (string) $numericRange);
        self::assertFalse($numericRange->isEmpty());
    }

    #[Test]
    public function can_create_empty_range(): void
    {
        $numericRange = NumericRange::empty();

        self::assertEquals('empty', (string) $numericRange);
        self::assertTrue($numericRange->isEmpty());
    }

    #[Test]
    public function can_create_infinite_range(): void
    {
        $numericRange = NumericRange::infinite();

        self::assertEquals('(,)', (string) $numericRange);
        self::assertFalse($numericRange->isEmpty());
    }

    #[Test]
    #[DataProvider('providesContainsTestCases')]
    public function can_check_contains(NumericRange $numericRange, mixed $value, bool $expected): void
    {
        self::assertEquals($expected, $numericRange->contains($value));
    }

    #[Test]
    #[DataProvider('providesFromStringTestCases')]
    public function can_parse_from_string(string $input, NumericRange $numericRange): void
    {
        $result = NumericRange::fromString($input);

        self::assertEquals($numericRange->__toString(), $result->__toString());
        self::assertEquals($numericRange->isEmpty(), $result->isEmpty());
    }

    public static function providesContainsTestCases(): \Generator
    {
        $numericRange = new NumericRange(1, 10);

        yield 'contains value in range' => [$numericRange, 5, true];
        yield 'contains lower bound (inclusive)' => [$numericRange, 1, true];
        yield 'does not contain upper bound (exclusive)' => [$numericRange, 10, false];
        yield 'does not contain value below range' => [$numericRange, 0, false];
        yield 'does not contain value above range' => [$numericRange, 11, false];
        yield 'does not contain null' => [$numericRange, null, false];

        $emptyRange = NumericRange::empty();
        yield 'empty range contains nothing' => [$emptyRange, 5, false];
    }

    public static function providesFromStringTestCases(): \Generator
    {
        yield 'simple range' => ['[1.5,10.7)', new NumericRange(1.5, 10.7)];
        yield 'inclusive range' => ['[1,10]', new NumericRange(1, 10, true, true)];
        yield 'exclusive range' => ['(1,10)', new NumericRange(1, 10, false, false)];
        yield 'infinite lower' => ['[,10)', new NumericRange(null, 10)];
        yield 'infinite upper' => ['[1,)', new NumericRange(1, null)];
        yield 'empty range' => ['empty', NumericRange::empty()];
    }

    #[Test]
    public function throws_exception_for_invalid_lower_bound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Lower bound must be numeric');

        new NumericRange('invalid', 10);
    }

    #[Test]
    public function throws_exception_for_invalid_upper_bound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Upper bound must be numeric');

        new NumericRange(1, 'invalid');
    }
}
