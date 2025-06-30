<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class Int8RangeTest extends TestCase
{
    #[Test]
    public function can_create_simple_range(): void
    {
        $int8Range = new Int8Range(1, 1000);

        self::assertEquals('[1,1000)', (string) $int8Range);
        self::assertFalse($int8Range->isEmpty());
    }

    #[Test]
    public function can_create_empty_range(): void
    {
        $int8Range = Int8Range::empty();

        self::assertEquals('empty', (string) $int8Range);
        self::assertTrue($int8Range->isEmpty());
    }

    #[Test]
    public function can_create_infinite_range(): void
    {
        $int8Range = Int8Range::infinite();

        self::assertEquals('(,)', (string) $int8Range);
        self::assertFalse($int8Range->isEmpty());
    }

    #[Test]
    public function can_create_inclusive_range(): void
    {
        $int8Range = Int8Range::inclusive(1, 10);

        self::assertEquals('[1,10]', (string) $int8Range);
        self::assertFalse($int8Range->isEmpty());
    }

    #[Test]
    public function can_handle_large_values(): void
    {
        $int8Range = new Int8Range(PHP_INT_MIN, PHP_INT_MAX);

        self::assertEquals('['.PHP_INT_MIN.','.PHP_INT_MAX.')', (string) $int8Range);
        self::assertFalse($int8Range->isEmpty());
    }

    #[Test]
    #[DataProvider('providesContainsTestCases')]
    public function can_check_contains(Int8Range $int8Range, mixed $value, bool $expected): void
    {
        self::assertEquals($expected, $int8Range->contains($value));
    }

    #[Test]
    #[DataProvider('providesFromStringTestCases')]
    public function can_parse_from_string(string $input, Int8Range $int8Range): void
    {
        $result = Int8Range::fromString($input);

        self::assertEquals($int8Range->__toString(), $result->__toString());
        self::assertEquals($int8Range->isEmpty(), $result->isEmpty());
    }

    public static function providesContainsTestCases(): \Generator
    {
        $int8Range = new Int8Range(1, 10);

        yield 'contains value in range' => [$int8Range, 5, true];
        yield 'contains lower bound (inclusive)' => [$int8Range, 1, true];
        yield 'does not contain upper bound (exclusive)' => [$int8Range, 10, false];
        yield 'does not contain value below range' => [$int8Range, 0, false];
        yield 'does not contain value above range' => [$int8Range, 11, false];
        yield 'does not contain null' => [$int8Range, null, false];

        $emptyRange = Int8Range::empty();
        yield 'empty range contains nothing' => [$emptyRange, 5, false];
    }

    public static function providesFromStringTestCases(): \Generator
    {
        yield 'simple range' => ['[1,1000)', new Int8Range(1, 1000)];
        yield 'inclusive range' => ['[1,10]', new Int8Range(1, 10, true, true)];
        yield 'exclusive range' => ['(1,10)', new Int8Range(1, 10, false, false)];
        yield 'infinite lower' => ['[,10)', new Int8Range(null, 10)];
        yield 'infinite upper' => ['[1,)', new Int8Range(1, null)];
        yield 'empty range' => ['empty', Int8Range::empty()];
        yield 'large values' => ['['.PHP_INT_MIN.','.PHP_INT_MAX.')', new Int8Range(PHP_INT_MIN, PHP_INT_MAX)];
    }
}
