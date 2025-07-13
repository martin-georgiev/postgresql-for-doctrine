<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends BaseRangeTestCase<int>
 */
final class Int8RangeTest extends BaseRangeTestCase
{
    protected function createSimpleRange(): Range
    {
        return new Int8Range(1, 1000);
    }

    protected function getExpectedSimpleRangeString(): string
    {
        return '[1,1000)';
    }

    protected function createEmptyRange(): Range
    {
        return Int8Range::empty();
    }

    protected function createInfiniteRange(): Range
    {
        return Int8Range::infinite();
    }

    protected function createInclusiveRange(): Range
    {
        return new Int8Range(1, 10, true, true);
    }

    protected function getExpectedInclusiveRangeString(): string
    {
        return '[1,10]';
    }

    protected function parseFromString(string $input): Range
    {
        return Int8Range::fromString($input);
    }

    protected function createBoundaryTestRange(): Range
    {
        return new Int8Range(1, 10, true, false); // [1, 10)
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
                'range' => new Int8Range(10, 5),
                'expectedEmpty' => true,
            ],
            'normal range should not be empty' => [
                'range' => new Int8Range(5, 10),
                'expectedEmpty' => false,
            ],
            'equal bounds exclusive should be empty' => [
                'range' => new Int8Range(5, 5, false, false),
                'expectedEmpty' => true,
            ],
            'equal bounds inclusive should not be empty' => [
                'range' => new Int8Range(5, 5, true, true),
                'expectedEmpty' => false,
            ],
        ];
    }

    #[Test]
    public function can_handle_large_values(): void
    {
        $int8Range = new Int8Range(PHP_INT_MIN, PHP_INT_MAX);

        self::assertEquals('['.PHP_INT_MIN.','.PHP_INT_MAX.')', (string) $int8Range);
        self::assertFalse($int8Range->isEmpty());
    }

    public static function provideContainsTestCases(): \Generator
    {
        $int8Range = new Int8Range(1, 10);

        yield 'contains middle value' => [$int8Range, 5, true];
        yield 'contains lower bound' => [$int8Range, 1, true];
        yield 'excludes upper bound' => [$int8Range, 10, false];
        yield 'excludes below range' => [$int8Range, 0, false];
        yield 'excludes above range' => [$int8Range, 11, false];
        yield 'excludes null' => [$int8Range, null, false];
    }

    public static function provideFromStringTestCases(): \Generator
    {
        yield 'basic range' => ['[1,10)', new Int8Range(1, 10)];
        yield 'inclusive' => ['[1,10]', new Int8Range(1, 10, true, true)];
        yield 'exclusive' => ['(1,10)', new Int8Range(1, 10, false, false)];
        yield 'infinite lower' => ['[,10)', new Int8Range(null, 10)];
        yield 'infinite upper' => ['[1,)', new Int8Range(1, null)];
        yield 'empty' => ['empty', Int8Range::empty()];
        yield 'large values' => ['['.PHP_INT_MIN.','.PHP_INT_MAX.')', new Int8Range(PHP_INT_MIN, PHP_INT_MAX)];
    }
}
