<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidRangeForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends BaseRangeTestCase<float|int>
 */
final class NumericRangeTest extends BaseRangeTestCase
{
    protected function createSimpleRange(): Range
    {
        return new NumericRange(1.5, 10.7);
    }

    protected function getExpectedSimpleRangeString(): string
    {
        return '[1.5,10.7)';
    }

    protected function createEmptyRange(): Range
    {
        return NumericRange::empty();
    }

    protected function createInfiniteRange(): Range
    {
        return NumericRange::infinite();
    }

    protected function createInclusiveRange(): Range
    {
        return new NumericRange(1, 10, true, true);
    }

    protected function getExpectedInclusiveRangeString(): string
    {
        return '[1,10]';
    }

    protected function parseFromString(string $input): Range
    {
        return NumericRange::fromString($input);
    }

    protected function createBoundaryTestRange(): Range
    {
        return new NumericRange(1, 10, true, false); // [1, 10)
    }

    protected function getBoundaryTestCases(): array
    {
        return [
            'contains lower bound (inclusive)' => ['value' => 1, 'expected' => true],
            'does not contain value below range' => ['value' => 0, 'expected' => false],
            'does not contain upper bound (exclusive)' => ['value' => 10, 'expected' => false],
            'contains value just below upper' => ['value' => 9.9, 'expected' => true],
            'does not contain value above range' => ['value' => 11, 'expected' => false],
            'contains middle value' => ['value' => 5.5, 'expected' => true],
        ];
    }

    protected function getComparisonTestCases(): array
    {
        return [
            'reverse range should be empty' => [
                'range' => new NumericRange(10.5, 5.0),
                'expectedEmpty' => true,
            ],
            'normal range should not be empty' => [
                'range' => new NumericRange(5.0, 10.5),
                'expectedEmpty' => false,
            ],
            'equal bounds exclusive should be empty' => [
                'range' => new NumericRange(5.0, 5.0, false, false),
                'expectedEmpty' => true,
            ],
            'equal bounds inclusive should not be empty' => [
                'range' => new NumericRange(5.0, 5.0, true, true),
                'expectedEmpty' => false,
            ],
        ];
    }

    public static function provideContainsTestCases(): \Generator
    {
        $numericRange = new NumericRange(1, 10);

        yield 'contains middle value' => [$numericRange, 5, true];
        yield 'contains lower bound' => [$numericRange, 1, true];
        yield 'excludes upper bound' => [$numericRange, 10, false];
        yield 'excludes below range' => [$numericRange, 0, false];
        yield 'excludes above range' => [$numericRange, 11, false];
        yield 'excludes null' => [$numericRange, null, false];
        yield 'empty range excludes any value' => [NumericRange::empty(), 5, false];

        $unboundedLower = new NumericRange(null, 10);
        yield 'unbounded lower contains value in range' => [$unboundedLower, 5, true];
        yield 'unbounded lower excludes upper bound' => [$unboundedLower, 10, false];

        $unboundedUpper = new NumericRange(1, null);
        yield 'unbounded upper contains value in range' => [$unboundedUpper, 100, true];
        yield 'unbounded upper excludes below lower' => [$unboundedUpper, 0, false];
    }

    public static function provideFromStringTestCases(): \Generator
    {
        yield 'simple range' => ['[1.5,10.7)', new NumericRange(1.5, 10.7)];
        yield 'inclusive range' => ['[1,10]', new NumericRange(1, 10, true, true)];
        yield 'exclusive range' => ['(1,10)', new NumericRange(1, 10, false, false)];
        yield 'unbounded lower' => ['[,10)', new NumericRange(null, 10)];
        yield 'bounded by negative infinity' => ['[-Infinity,10)', new NumericRange(-INF, 10)];
        yield 'unbounded upper' => ['[1,)', new NumericRange(1, null)];
        yield 'bounded by positive infinity' => ['[1,Infinity)', new NumericRange(1, INF)];
        yield 'bounded by abbreviated negative infinity' => ['[-inf,10)', new NumericRange(-INF, 10)];
        yield 'bounded by abbreviated positive infinity' => ['[1,inf)', new NumericRange(1, INF)];
        yield 'empty range' => ['empty', NumericRange::empty()];
    }

    #[Test]
    public function throws_exception_for_invalid_lower_bound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Lower bound must be numeric');

        /* @phpstan-ignore-next-line Intentionally testing invalid input */
        new NumericRange('invalid', 10);
    }

    #[Test]
    public function throws_exception_for_invalid_upper_bound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Upper bound must be numeric');

        /* @phpstan-ignore-next-line Intentionally testing invalid input */
        new NumericRange(1, 'invalid');
    }

    #[Test]
    public function throws_exception_for_invalid_numeric_bound_in_comparison_via_contains(): void
    {
        $numericRange = new NumericRange(1, 10);

        $this->expectException(InvalidRangeForPHPException::class);
        $this->expectExceptionMessage('Range bound must be numeric');

        $numericRange->contains('invalid');
    }

    #[DataProvider('provideInvalidFromStringInputs')]
    #[Test]
    public function throws_exception_for_invalid_from_string_input(string $input, string $expectedMessage): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        NumericRange::fromString($input);
    }

    public static function provideInvalidFromStringInputs(): \Generator
    {
        yield 'non-numeric value' => ['[not_numeric,10)', 'Invalid numeric value'];
        yield 'invalid format' => ['invalid_format', 'Invalid range format'];
        yield 'missing brackets' => ['1,10', 'Invalid range format'];
    }

    #[Test]
    public function parses_integer_and_float_values_via_from_string(): void
    {
        $numericRange = NumericRange::fromString('[42,100)');
        $this->assertSame('[42,100)', (string) $numericRange);

        $range2 = NumericRange::fromString('[-123,0)');
        $this->assertSame('[-123,0)', (string) $range2);

        $range3 = NumericRange::fromString('[3.14,10)');
        $this->assertSame('[3.14,10)', (string) $range3);

        $range4 = NumericRange::fromString('[-2.5,0)');
        $this->assertSame('[-2.5,0)', (string) $range4);
    }

    #[Test]
    public function accepts_mixed_integer_and_float_values(): void
    {
        $range = new NumericRange(1, 10.5);
        $this->assertSame('[1,10.5)', (string) $range);

        $range2 = new NumericRange(1.5, 10);
        $this->assertSame('[1.5,10)', (string) $range2);
    }

    #[Test]
    public function compares_mixed_numeric_types_via_is_empty(): void
    {
        $reverseRange = new NumericRange(5.1, 5.0);
        $this->assertTrue($reverseRange->isEmpty());

        $normalRange = new NumericRange(5.0, 5.1);
        $this->assertFalse($normalRange->isEmpty());

        $equalRange = new NumericRange(5, 5.0, true, true);
        $this->assertFalse($equalRange->isEmpty());

        $equalExclusive = new NumericRange(5.0, 5.0, false, false);
        $this->assertTrue($equalExclusive->isEmpty());
    }

    #[Test]
    public function formats_numeric_values_via_to_string(): void
    {
        $range1 = new NumericRange(42, 100);
        $this->assertSame('[42,100)', (string) $range1);

        $range2 = new NumericRange(3.14, 10);
        $this->assertSame('[3.14,10)', (string) $range2);

        $range3 = new NumericRange(-2.5, 0);
        $this->assertSame('[-2.5,0)', (string) $range3);
    }

    #[DataProvider('providePhpInfConstantCases')]
    #[Test]
    public function creates_range_with_php_inf_constant(
        float|int|null $lower,
        float|int|null $upper,
        string $expectedString,
        bool $expectedLowerBoundedInfinity,
        bool $expectedUpperBoundedInfinity,
    ): void {
        $numericRange = new NumericRange($lower, $upper);

        $this->assertSame($expectedString, (string) $numericRange);
        $this->assertSame($expectedLowerBoundedInfinity, $numericRange->isLowerBoundedInfinity());
        $this->assertSame($expectedUpperBoundedInfinity, $numericRange->isUpperBoundedInfinity());
    }

    public static function providePhpInfConstantCases(): \Generator
    {
        yield 'upper bounded infinity' => [0, INF, '[0,Infinity)', false, true];
        yield 'lower bounded infinity' => [-INF, 100, '[-Infinity,100)', true, false];
        yield 'both bounds infinity' => [-INF, INF, '[-Infinity,Infinity)', true, true];
    }

    #[DataProvider('provideInfinitySpellings')]
    #[Test]
    public function parses_every_accepted_infinity_spelling(string $bound): void
    {
        $numericRange = NumericRange::fromString(\sprintf('[1,%s)', $bound));

        $this->assertSame('[1,Infinity)', (string) $numericRange);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInfinitySpellings(): array
    {
        return [
            'canonical' => ['Infinity'],
            'lowercase' => ['infinity'],
            'uppercase' => ['INFINITY'],
            'mixed case' => ['InFiNiTy'],
            'explicit plus' => ['+infinity'],
            'abbreviated' => ['inf'],
            'abbreviated uppercase' => ['INF'],
            'abbreviated with explicit plus' => ['+inf'],
        ];
    }

    #[DataProvider('provideNegativeInfinitySpellings')]
    #[Test]
    public function parses_every_accepted_negative_infinity_spelling(string $bound): void
    {
        $numericRange = NumericRange::fromString(\sprintf('[%s,1)', $bound));

        $this->assertSame('[-Infinity,1)', (string) $numericRange);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideNegativeInfinitySpellings(): array
    {
        return [
            'canonical' => ['-Infinity'],
            'lowercase' => ['-infinity'],
            'abbreviated' => ['-inf'],
            'abbreviated uppercase' => ['-INF'],
        ];
    }

    /**
     * PostgreSQL orders NaN above every other numeric bound instead of treating it as an open end, so it is a bound
     * in its own right rather than an infinity spelling.
     */
    #[DataProvider('provideNotANumberSpellings')]
    #[Test]
    public function parses_every_accepted_not_a_number_spelling(string $bound): void
    {
        $numericRange = NumericRange::fromString(\sprintf('[1,%s)', $bound));

        $this->assertNan($numericRange->getUpper());
        $this->assertSame('[1,NaN)', (string) $numericRange);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideNotANumberSpellings(): array
    {
        return [
            'canonical' => ['NaN'],
            'lowercase' => ['nan'],
            'uppercase' => ['NAN'],
        ];
    }

    /**
     * `numeric` reads a narrower NaN grammar than `float8` does: `SELECT '-nan'::numeric` is an error while
     * `SELECT '-nan'::float8` is not.
     */
    #[DataProvider('provideSignedNotANumberSpellings')]
    #[Test]
    public function throws_exception_for_a_signed_not_a_number_bound(string $bound): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid numeric value');

        NumericRange::fromString(\sprintf('[1,%s)', $bound));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideSignedNotANumberSpellings(): array
    {
        return [
            'negative' => ['-nan'],
            'explicitly positive' => ['+nan'],
        ];
    }

    #[Test]
    public function php_inf_constant_is_equivalent_to_infinity_flags(): void
    {
        $rangeWithInf = new NumericRange(0, INF);
        $rangeWithFlag = new NumericRange(0, null, true, false, false, false, true);

        $this->assertSame((string) $rangeWithInf, (string) $rangeWithFlag);
        $this->assertSame($rangeWithInf->isUpperBoundedInfinity(), $rangeWithFlag->isUpperBoundedInfinity());
    }

    /**
     * A bare `(string)` cast of NAN emits `NAN` and raises a PHP warning, neither of which PostgreSQL reads back.
     */
    #[Test]
    public function keeps_a_not_a_number_bound(): void
    {
        $this->assertSame('[1,NaN)', (string) new NumericRange(1, \NAN));
        $this->assertSame('[NaN,)', (string) new NumericRange(\NAN, null));
        $this->assertSame('[NaN,NaN]', (string) new NumericRange(\NAN, \NAN, true, true));
    }

    /**
     * PostgreSQL gives `numeric` a total order that puts NaN above every other value, `Infinity` included, and treats
     * it as equal to itself. PHP's spaceship operator answers 1 for every comparison involving NAN, so the ordering
     * has to be spelled out.
     */
    #[DataProvider('provideNotANumberOrderingCases')]
    #[Test]
    public function orders_a_not_a_number_bound_above_every_other_value(string $range, mixed $target, bool $isContained): void
    {
        $this->assertSame($isContained, NumericRange::fromString($range)->contains($target));
    }

    /**
     * @return array<string, array{string, mixed, bool}>
     */
    public static function provideNotANumberOrderingCases(): array
    {
        return [
            'exclusive NaN upper bound excludes NaN' => ['[1,NaN)', \NAN, false],
            'inclusive NaN upper bound contains NaN' => ['[1,NaN]', \NAN, true],
            'NaN upper bound contains a huge finite value' => ['[1,NaN)', 1e300, true],
            'NaN upper bound excludes a value below the lower bound' => ['[1,NaN)', 0, false],
            'NaN lower bound excludes a finite value' => ['[NaN,)', 5, false],
            'NaN lower bound contains NaN' => ['[NaN,)', \NAN, true],
            'infinite upper bound excludes NaN' => ['[1,Infinity)', \NAN, false],
            'inclusive infinite upper bound excludes NaN' => ['[1,Infinity]', \NAN, false],
            'negative infinite lower bound contains NaN' => ['[-Infinity,)', \NAN, true],
            'unbounded range contains NaN' => ['(,)', \NAN, true],
        ];
    }

    /**
     * Two NaN bounds are equal, so the same bracket rules PostgreSQL applies to any other pair of equal bounds decide
     * whether the range is empty.
     */
    #[Test]
    public function treats_a_range_between_two_exclusive_not_a_number_bounds_as_empty(): void
    {
        $this->assertSame('empty', (string) NumericRange::fromString('[NaN,NaN)'));
        $this->assertSame('[NaN,NaN]', (string) NumericRange::fromString('[NaN,NaN]'));
    }

    #[Test]
    public function throws_exception_for_an_overflowing_literal_when_parsing(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        NumericRange::fromString('[1,1e999)');
    }

    #[Test]
    public function keeps_an_explicit_infinity_bound(): void
    {
        $this->assertSame('[1,Infinity)', (string) new NumericRange(1, \INF));
    }
}
