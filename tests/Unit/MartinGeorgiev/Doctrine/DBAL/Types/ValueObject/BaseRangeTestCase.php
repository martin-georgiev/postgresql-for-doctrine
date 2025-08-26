<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @template R of int|float|\DateTimeInterface
 *
 * TODO: Remove PHPStan suppressions when covariant generics are supported
 *
 * @see https://github.com/phpstan/phpstan/issues/7427
 */
abstract class BaseRangeTestCase extends TestCase
{
    #[Test]
    public function can_create_simple_range(): void
    {
        $range = $this->createSimpleRange();
        $expectedString = $this->getExpectedSimpleRangeString();

        $this->assertEquals($expectedString, (string) $range);
        $this->assertFalse($range->isEmpty());
    }

    #[Test]
    public function can_create_empty_range(): void
    {
        $range = $this->createEmptyRange();

        $this->assertEquals('empty', (string) $range);
        $this->assertTrue($range->isEmpty());
    }

    #[Test]
    public function can_create_infinite_range(): void
    {
        $range = $this->createInfiniteRange();

        $this->assertEquals('(,)', (string) $range);
        $this->assertFalse($range->isEmpty());
    }

    #[Test]
    public function can_create_inclusive_range(): void
    {
        $range = $this->createInclusiveRange();
        $expectedString = $this->getExpectedInclusiveRangeString();

        $this->assertEquals($expectedString, (string) $range);
        $this->assertFalse($range->isEmpty());
    }

    /**
     * @param Range<R> $range
     */
    #[Test]
    #[DataProvider('provideContainsTestCases')]
    public function can_check_contains(Range $range, mixed $value, bool $expected): void
    {
        $this->assertEquals($expected, $range->contains($value));
    }

    /**
     * @return \Generator<string, array{Range<R>, mixed, bool}>
     */
    abstract public static function provideContainsTestCases(): \Generator;

    /**
     * @param Range<R> $expectedRange
     */
    #[Test]
    #[DataProvider('provideFromStringTestCases')]
    public function can_parse_from_string(string $input, Range $expectedRange): void
    {
        $range = $this->parseFromString($input);
        $this->assertRangeEquals($expectedRange, $range);
    }

    /**
     * @return \Generator<string, array{string, Range<R>}>
     */
    abstract public static function provideFromStringTestCases(): \Generator;

    #[Test]
    public function can_handle_boundary_conditions(): void
    {
        $range = $this->createBoundaryTestRange();
        $testCases = $this->getBoundaryTestCases();

        $this->assertBoundaryConditions($range, $testCases);
    }

    #[Test]
    public function can_handle_comparison_via_is_empty(): void
    {
        $testCases = $this->getComparisonTestCases();

        foreach ($testCases as $description => $testCase) {
            $this->assertEquals(
                $testCase['expectedEmpty'],
                $testCase['range']->isEmpty(),
                'Comparison test failed: '.$description
            );
        }
    }

    /**
     * Create a simple range for basic testing.
     *
     * @return Range<R>
     */
    abstract protected function createSimpleRange(): Range;

    /**
     * Get expected string representation of simple range.
     */
    abstract protected function getExpectedSimpleRangeString(): string;

    /**
     * Create an empty range.
     *
     * @return Range<R>
     */
    abstract protected function createEmptyRange(): Range;

    /**
     * Create an infinite range.
     *
     * @return Range<R>
     */
    abstract protected function createInfiniteRange(): Range;

    /**
     * Create an inclusive range for testing.
     *
     * @return Range<R>
     */
    abstract protected function createInclusiveRange(): Range;

    /**
     * Get expected string representation of inclusive range.
     */
    abstract protected function getExpectedInclusiveRangeString(): string;

    /**
     * Parse range from string.
     *
     * @return Range<R>
     */
    abstract protected function parseFromString(string $input): Range;

    /**
     * Create range for boundary testing.
     *
     * @return Range<R>
     */
    abstract protected function createBoundaryTestRange(): Range;

    /**
     * Get boundary test cases.
     *
     * @return array<string, array{value: mixed, expected: bool}>
     */
    abstract protected function getBoundaryTestCases(): array;

    /**
     * Get comparison test cases.
     *
     * @return array<string, array{range: Range<R>, expectedEmpty: bool}>
     */
    abstract protected function getComparisonTestCases(): array;

    /**
     * Assert that a range equals another range by comparing string representation and isEmpty state.
     *
     * @param Range<R> $expected
     * @param Range<R> $actual
     */
    protected function assertRangeEquals(Range $expected, Range $actual, string $message = ''): void
    {
        $this->assertEquals($expected->__toString(), $actual->__toString(), $message.' (string representation)');
        $this->assertEquals($expected->isEmpty(), $actual->isEmpty(), $message.' (isEmpty state)');
    }

    /**
     * Assert that a range contains all the given values.
     *
     * @param Range<R> $range
     * @param array<mixed> $values
     */
    protected function assertRangeContainsAll(Range $range, array $values, string $message = ''): void
    {
        foreach ($values as $value) {
            $this->assertTrue(
                $range->contains($value),
                $message.' - Range should contain value: '.\var_export($value, true)
            );
        }
    }

    /**
     * Assert that a range does not contain any of the given values.
     *
     * @param Range<R> $range
     * @param array<mixed> $values
     */
    protected function assertRangeContainsNone(Range $range, array $values, string $message = ''): void
    {
        foreach ($values as $value) {
            $this->assertFalse(
                $range->contains($value),
                $message.' - Range should not contain value: '.\var_export($value, true)
            );
        }
    }

    /**
     * Assert that a range has the expected string representation.
     *
     * @param Range<R> $range
     */
    protected function assertRangeStringEquals(string $expected, Range $range, string $message = ''): void
    {
        $this->assertEquals($expected, (string) $range, $message);
    }

    /**
     * Assert that a range is empty.
     *
     * @param Range<R> $range
     */
    protected function assertRangeIsEmpty(Range $range, string $message = ''): void
    {
        $this->assertTrue($range->isEmpty(), $message.' - Range should be empty');
        $this->assertEquals('empty', (string) $range, $message.' - Empty range should have "empty" string representation');
    }

    /**
     * Assert that a range is not empty.
     *
     * @param Range<R> $range
     */
    protected function assertRangeIsNotEmpty(Range $range, string $message = ''): void
    {
        $this->assertFalse($range->isEmpty(), $message.' - Range should not be empty');
        $this->assertNotEquals('empty', (string) $range, $message.' - Non-empty range should not have "empty" string representation');
    }

    /**
     * Test boundary conditions for a range with known bounds.
     *
     * @param Range<R> $range
     * @param array<string, array{value: mixed, expected: bool}> $testCases
     */
    protected function assertBoundaryConditions(Range $range, array $testCases, string $message = ''): void
    {
        foreach ($testCases as $description => $testCase) {
            $this->assertEquals(
                $testCase['expected'],
                $range->contains($testCase['value']),
                $message.' - Boundary test failed: '.$description
            );
        }
    }

    /**
     * Generate common boundary test cases for a range [lower, upper).
     *
     * @return array<string, array{value: mixed, expected: bool}>
     */
    protected function generateBoundaryTestCases(
        mixed $lower,
        mixed $upper,
        mixed $belowLower,
        mixed $aboveUpper,
        mixed $middle
    ): array {
        return [
            'contains lower bound (inclusive)' => ['value' => $lower, 'expected' => true],
            'does not contain value below range' => ['value' => $belowLower, 'expected' => false],
            'does not contain upper bound (exclusive)' => ['value' => $upper, 'expected' => false],
            'does not contain value above range' => ['value' => $aboveUpper, 'expected' => false],
            'contains middle value' => ['value' => $middle, 'expected' => true],
            'does not contain null' => ['value' => null, 'expected' => false],
        ];
    }

    /**
     * Test that a range correctly handles equal bounds with different bracket combinations.
     *
     * @param callable(mixed, mixed, bool, bool): Range<R> $rangeFactory Function that creates a range
     */
    protected function assertEqualBoundsHandling(callable $rangeFactory, mixed $value): void
    {
        $inclusiveEqual = $rangeFactory($value, $value, true, true);
        $this->assertFalse($inclusiveEqual->isEmpty(), 'Equal bounds with inclusive brackets should not be empty');
        $this->assertTrue($inclusiveEqual->contains($value), 'Equal bounds with inclusive brackets should contain the value');

        $exclusiveExclusive = $rangeFactory($value, $value, false, false);
        $this->assertTrue($exclusiveExclusive->isEmpty(), 'Equal bounds with exclusive brackets should be empty');

        $inclusiveExclusive = $rangeFactory($value, $value, true, false);
        $this->assertTrue($inclusiveExclusive->isEmpty(), 'Equal bounds with mixed brackets should be empty');

        $exclusiveInclusive = $rangeFactory($value, $value, false, true);
        $this->assertTrue($exclusiveInclusive->isEmpty(), 'Equal bounds with mixed brackets should be empty');
    }

    /**
     * Test that a range correctly handles reverse bounds (lower > upper).
     *
     * @param callable(mixed, mixed, bool, bool): Range<R> $rangeFactory Function that creates a range
     */
    protected function assertReverseBoundsHandling(callable $rangeFactory, mixed $lower, mixed $upper): void
    {
        $reverseRange = $rangeFactory($lower, $upper, true, false);
        $this->assertTrue($reverseRange->isEmpty(), 'Range with lower > upper should be empty');
    }
}
