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

    #[Test]
    #[DataProvider('provideInvalidFromStringInputs')]
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
    public function can_parse_integer_and_float_values_via_from_string(): void
    {
        $numericRange = NumericRange::fromString('[42,100)');
        $this->assertStringContainsString('42', (string) $numericRange);

        $range2 = NumericRange::fromString('[-123,0)');
        $this->assertStringContainsString('-123', (string) $range2);

        $range3 = NumericRange::fromString('[3.14,10)');
        $this->assertStringContainsString('3.14', (string) $range3);

        $range4 = NumericRange::fromString('[-2.5,0)');
        $this->assertStringContainsString('-2.5', (string) $range4);
    }

    #[Test]
    public function can_handle_mixed_integer_and_float_ranges(): void
    {
        $range = new NumericRange(1, 10.5);
        $this->assertSame('[1,10.5)', (string) $range);

        $range2 = new NumericRange(1.5, 10);
        $this->assertSame('[1.5,10)', (string) $range2);
    }

    #[Test]
    public function can_compare_mixed_numeric_types_via_is_empty(): void
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
    public function can_format_numeric_values_via_to_string(): void
    {
        $range1 = new NumericRange(42, 100);
        $this->assertStringContainsString('42', (string) $range1);

        $range2 = new NumericRange(3.14, 10);
        $this->assertStringContainsString('3.14', (string) $range2);

        $range3 = new NumericRange(-2.5, 0);
        $this->assertStringContainsString('-2.5', (string) $range3);
    }

    #[Test]
    #[DataProvider('providePhpInfConstantCases')]
    public function can_create_range_with_php_inf_constant(
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
        yield 'upper bounded infinity' => [0, INF, '[0,infinity)', false, true];
        yield 'lower bounded infinity' => [-INF, 100, '[-infinity,100)', true, false];
        yield 'both bounds infinity' => [-INF, INF, '[-infinity,infinity)', true, true];
    }

    #[Test]
    public function php_inf_constant_is_equivalent_to_infinity_flags(): void
    {
        $rangeWithInf = new NumericRange(0, INF);
        $rangeWithFlag = new NumericRange(0, null, true, false, false, false, true);

        $this->assertSame((string) $rangeWithInf, (string) $rangeWithFlag);
        $this->assertSame($rangeWithInf->isUpperBoundedInfinity(), $rangeWithFlag->isUpperBoundedInfinity());
    }
}
