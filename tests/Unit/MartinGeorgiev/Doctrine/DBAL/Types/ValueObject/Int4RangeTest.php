<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Range;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends BaseRangeTestCase<int>
 */
final class Int4RangeTest extends BaseRangeTestCase
{
    protected function createSimpleRange(): Range
    {
        return new Int4Range(1, 1000);
    }

    protected function getExpectedSimpleRangeString(): string
    {
        return '[1,1000)';
    }

    protected function createEmptyRange(): Range
    {
        return Int4Range::empty();
    }

    protected function createInfiniteRange(): Range
    {
        return Int4Range::infinite();
    }

    protected function createInclusiveRange(): Range
    {
        return new Int4Range(1, 10, true, true);
    }

    protected function getExpectedInclusiveRangeString(): string
    {
        return '[1,10]';
    }

    protected function parseFromString(string $input): Range
    {
        return Int4Range::fromString($input);
    }

    protected function createBoundaryTestRange(): Range
    {
        return new Int4Range(1, 10, true, false); // [1, 10)
    }

    protected function getBoundaryTestCases(): array
    {
        return [
            'contains lower bound (inclusive)' => ['value' => 1, 'expected' => true],
            'does not contain value below range' => ['value' => 0, 'expected' => false],
            'does not contain upper bound (exclusive)' => ['value' => 10, 'expected' => false],
            'contains value just below upper' => ['value' => 9, 'expected' => true],
            'does not contain value above range' => ['value' => 11, 'expected' => false],
            'contains middle value' => ['value' => 5, 'expected' => true],
        ];
    }

    protected function getComparisonTestCases(): array
    {
        return [
            'reverse range should be empty' => [
                'range' => new Int4Range(10, 5),
                'expectedEmpty' => true,
            ],
            'normal range should not be empty' => [
                'range' => new Int4Range(5, 10),
                'expectedEmpty' => false,
            ],
            'equal bounds exclusive should be empty' => [
                'range' => new Int4Range(5, 5, false, false),
                'expectedEmpty' => true,
            ],
            'equal bounds inclusive should not be empty' => [
                'range' => new Int4Range(5, 5, true, true),
                'expectedEmpty' => false,
            ],
        ];
    }

    public static function provideContainsTestCases(): \Generator
    {
        $int4Range = new Int4Range(1, 10);

        yield 'contains middle value' => [$int4Range, 5, true];
        yield 'contains lower bound' => [$int4Range, 1, true];
        yield 'excludes upper bound' => [$int4Range, 10, false];
        yield 'excludes below range' => [$int4Range, 0, false];
        yield 'excludes above range' => [$int4Range, 11, false];
        yield 'excludes null' => [$int4Range, null, false];
    }

    public static function provideFromStringTestCases(): \Generator
    {
        yield 'basic range' => ['[1,10)', new Int4Range(1, 10)];
        yield 'inclusive' => ['[1,10]', new Int4Range(1, 10, true, true)];
        yield 'exclusive' => ['(1,10)', new Int4Range(1, 10, false, false)];
        yield 'infinite lower' => ['[,10)', new Int4Range(null, 10)];
        yield 'infinite upper' => ['[1,)', new Int4Range(1, null)];
        yield 'empty' => ['empty', Int4Range::empty()];
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
}
