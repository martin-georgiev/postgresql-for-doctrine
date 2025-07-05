<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Range;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class Int4RangeTest extends TestCase
{
    #[Test]
    public function can_create_simple_range(): void
    {
        $int4Range = new Int4Range(1, 1000);

        self::assertEquals('[1,1000)', (string) $int4Range);
        self::assertFalse($int4Range->isEmpty());
    }

    #[Test]
    public function can_create_empty_range(): void
    {
        $int4Range = Int4Range::empty();

        self::assertEquals('empty', (string) $int4Range);
        self::assertTrue($int4Range->isEmpty());
    }

    #[Test]
    public function can_create_infinite_range(): void
    {
        $int4Range = Int4Range::infinite();

        self::assertEquals('(,)', (string) $int4Range);
        self::assertFalse($int4Range->isEmpty());
    }

    #[Test]
    public function can_create_inclusive_range(): void
    {
        $int4Range = new Int4Range(1, 10, true, true);

        self::assertEquals('[1,10]', (string) $int4Range);
        self::assertFalse($int4Range->isEmpty());
    }

    #[Test]
    #[DataProvider('providesContainsTestCases')]
    public function can_check_contains(Int4Range $int4Range, mixed $value, bool $expected): void
    {
        self::assertEquals($expected, $int4Range->contains($value));
    }

    #[Test]
    #[DataProvider('providesFromStringTestCases')]
    public function can_parse_from_string(string $input, Int4Range $int4Range): void
    {
        $result = Int4Range::fromString($input);

        self::assertEquals($int4Range->__toString(), $result->__toString());
        self::assertEquals($int4Range->isEmpty(), $result->isEmpty());
    }

    #[Test]
    public function validates_int4_bounds_for_lower(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Lower bound -2147483649 is outside INT4 range');

        new Int4Range(-2147483649, 100);
    }

    #[Test]
    public function validates_int4_bounds_for_upper(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Upper bound 2147483648 is outside INT4 range');

        new Int4Range(100, 2147483648);
    }

    #[Test]
    public function allows_max_int4_values(): void
    {
        $int4Range = new Int4Range(-2147483648, 2147483647);

        self::assertEquals('[-2147483648,2147483647)', (string) $int4Range);
    }

    public static function providesContainsTestCases(): \Generator
    {
        $int4Range = new Int4Range(1, 10);

        yield 'contains value in range' => [$int4Range, 5, true];
        yield 'contains lower bound (inclusive)' => [$int4Range, 1, true];
        yield 'does not contain upper bound (exclusive)' => [$int4Range, 10, false];
        yield 'does not contain value below range' => [$int4Range, 0, false];
        yield 'does not contain value above range' => [$int4Range, 11, false];
        yield 'does not contain null' => [$int4Range, null, false];

        $emptyRange = Int4Range::empty();
        yield 'empty range contains nothing' => [$emptyRange, 5, false];
    }

    public static function providesFromStringTestCases(): \Generator
    {
        yield 'simple range' => ['[1,1000)', new Int4Range(1, 1000)];
        yield 'inclusive range' => ['[1,10]', new Int4Range(1, 10, true, true)];
        yield 'exclusive range' => ['(1,10)', new Int4Range(1, 10, false, false)];
        yield 'infinite lower' => ['[,10)', new Int4Range(null, 10)];
        yield 'infinite upper' => ['[1,)', new Int4Range(1, null)];
        yield 'empty range' => ['empty', Int4Range::empty()];
    }
}
