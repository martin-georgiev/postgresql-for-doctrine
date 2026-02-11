<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsRange;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends BaseTimestampRangeTestCase<\DateTimeInterface>
 */
final class TsRangeTest extends BaseTimestampRangeTestCase
{
    protected function createSimpleRange(): Range
    {
        return new TsRange(
            new \DateTimeImmutable('2023-01-01 10:00:00'),
            new \DateTimeImmutable('2023-01-01 18:00:00')
        );
    }

    protected function getExpectedSimpleRangeString(): string
    {
        return '[2023-01-01 10:00:00.000000,2023-01-01 18:00:00.000000)';
    }

    protected function createEmptyRange(): Range
    {
        return TsRange::empty();
    }

    protected function createInfiniteRange(): Range
    {
        return TsRange::infinite();
    }

    protected function createInclusiveRange(): Range
    {
        return new TsRange(
            new \DateTimeImmutable('2023-01-01 10:00:00'),
            new \DateTimeImmutable('2023-01-01 18:00:00'),
            true,
            true
        );
    }

    protected function getExpectedInclusiveRangeString(): string
    {
        return '[2023-01-01 10:00:00.000000,2023-01-01 18:00:00.000000]';
    }

    protected function parseFromString(string $input): Range
    {
        return TsRange::fromString($input);
    }

    protected function createBoundaryTestRange(): Range
    {
        return new TsRange($this->getTestStartTime(), $this->getTestEndTime(), true, false);
    }

    protected function createRangeWithTimes(
        ?\DateTimeInterface $start,
        ?\DateTimeInterface $end,
        bool $lowerInclusive = true,
        bool $upperInclusive = false
    ): Range {
        return new TsRange($start, $end, $lowerInclusive, $upperInclusive);
    }

    protected function getTestStartTime(): \DateTimeInterface
    {
        return new \DateTimeImmutable('2023-01-01 10:00:00');
    }

    protected function getTestEndTime(): \DateTimeInterface
    {
        return new \DateTimeImmutable('2023-01-01 18:00:00');
    }

    protected function getTimeBeforeRange(): \DateTimeInterface
    {
        return new \DateTimeImmutable('2023-01-01 09:00:00');
    }

    protected function getTimeInMiddle(): \DateTimeInterface
    {
        return new \DateTimeImmutable('2023-01-01 14:00:00');
    }

    protected function createTimeWithMicroseconds(string $timeString): \DateTimeInterface
    {
        return new \DateTimeImmutable($timeString);
    }

    protected function getTestStartTimeString(): string
    {
        return '2023-01-01 10:00:00';
    }

    protected function getTestEndTimeString(): string
    {
        return '2023-01-01 18:00:00';
    }

    protected function getBoundaryTestCases(): array
    {
        return [
            'contains lower bound (inclusive)' => [
                'value' => new \DateTimeImmutable('2023-01-01 10:00:00'),
                'expected' => true,
            ],
            'does not contain value before range' => [
                'value' => new \DateTimeImmutable('2023-01-01 09:00:00'),
                'expected' => false,
            ],
            'does not contain upper bound (exclusive)' => [
                'value' => new \DateTimeImmutable('2023-01-01 18:00:00'),
                'expected' => false,
            ],
            'contains value in middle' => [
                'value' => new \DateTimeImmutable('2023-01-01 14:00:00'),
                'expected' => true,
            ],
        ];
    }

    protected function getComparisonTestCases(): array
    {
        return [
            'reverse range should be empty' => [
                'range' => new TsRange(
                    new \DateTimeImmutable('2023-01-01 18:00:00'),
                    new \DateTimeImmutable('2023-01-01 10:00:00')
                ),
                'expectedEmpty' => true,
            ],
            'normal range should not be empty' => [
                'range' => new TsRange(
                    new \DateTimeImmutable('2023-01-01 10:00:00'),
                    new \DateTimeImmutable('2023-01-01 18:00:00')
                ),
                'expectedEmpty' => false,
            ],
            'equal bounds exclusive should be empty' => [
                'range' => new TsRange(
                    new \DateTimeImmutable('2023-01-01 10:00:00'),
                    new \DateTimeImmutable('2023-01-01 10:00:00'),
                    false,
                    false
                ),
                'expectedEmpty' => true,
            ],
            'equal bounds inclusive should not be empty' => [
                'range' => new TsRange(
                    new \DateTimeImmutable('2023-01-01 10:00:00'),
                    new \DateTimeImmutable('2023-01-01 10:00:00'),
                    true,
                    true
                ),
                'expectedEmpty' => false,
            ],
        ];
    }

    #[Test]
    public function can_create_hour_range(): void
    {
        $start = new \DateTimeImmutable('2023-01-01 14:00:00');
        $end = $start->modify('+1 hour');
        $tsRange = new TsRange($start, $end);

        $this->assertEquals('[2023-01-01 14:00:00.000000,2023-01-01 15:00:00.000000)', (string) $tsRange);
        $this->assertFalse($tsRange->isEmpty());
    }

    public static function provideContainsTestCases(): \Generator
    {
        $start = new \DateTimeImmutable('2023-01-01 10:00:00');
        $end = new \DateTimeImmutable('2023-01-01 18:00:00');
        $tsRange = new TsRange($start, $end);

        yield 'contains value in range' => [
            $tsRange,
            new \DateTimeImmutable('2023-01-01 14:00:00'),
            true,
        ];
        yield 'contains lower bound (inclusive)' => [$tsRange, $start, true];
        yield 'does not contain upper bound (exclusive)' => [$tsRange, $end, false];
        yield 'does not contain value before range' => [
            $tsRange,
            new \DateTimeImmutable('2023-01-01 09:00:00'),
            false,
        ];
        yield 'does not contain null' => [$tsRange, null, false];
    }

    public static function provideFromStringTestCases(): \Generator
    {
        yield 'simple range' => [
            '[2023-01-01 10:00:00.000000,2023-01-01 18:00:00.000000)',
            new TsRange(
                new \DateTimeImmutable('2023-01-01 10:00:00'),
                new \DateTimeImmutable('2023-01-01 18:00:00')
            ),
        ];
        yield 'inclusive range' => [
            '[2023-01-01 10:00:00.000000,2023-01-01 18:00:00.000000]',
            new TsRange(
                new \DateTimeImmutable('2023-01-01 10:00:00'),
                new \DateTimeImmutable('2023-01-01 18:00:00'),
                true,
                true
            ),
        ];
        yield 'unbounded lower' => [
            '[,2023-01-01 18:00:00.000000)',
            new TsRange(null, new \DateTimeImmutable('2023-01-01 18:00:00')),
        ];
        yield 'unbounded upper' => [
            '[2023-01-01 10:00:00.000000,)',
            new TsRange(new \DateTimeImmutable('2023-01-01 10:00:00'), null),
        ];
        yield 'bounded by negative infinity' => [
            '[-infinity,2023-01-01 18:00:00.000000)',
            new TsRange(null, new \DateTimeImmutable('2023-01-01 18:00:00'), true, false, false, true, false),
        ];
        yield 'bounded by positive infinity' => [
            '[2023-01-01 10:00:00.000000,infinity)',
            new TsRange(new \DateTimeImmutable('2023-01-01 10:00:00'), null, true, false, false, false, true),
        ];
        yield 'empty range' => ['empty', TsRange::empty()];
    }

    #[Test]
    public function can_handle_microseconds_correctly(): void
    {
        $start = new \DateTimeImmutable('2023-01-01 10:00:00.123456');
        $end = new \DateTimeImmutable('2023-01-01 18:00:00.654321');
        $tsRange = new TsRange($start, $end);

        $this->assertEquals('[2023-01-01 10:00:00.123456,2023-01-01 18:00:00.654321)', (string) $tsRange);
    }

    #[Test]
    public function throws_exception_for_invalid_value_in_constructor(): void
    {
        $this->expectException(\TypeError::class);

        /* @phpstan-ignore-next-line */
        new TsRange('invalid', new \DateTimeImmutable('2023-01-01 18:00:00'));
    }

    #[Test]
    public function can_format_timestamp_values_via_to_string(): void
    {
        $tsRange = new TsRange(
            new \DateTimeImmutable('2023-06-15 14:30:25.123456'),
            new \DateTimeImmutable('2023-06-15 18:45:30.654321')
        );

        $formatted = (string) $tsRange;
        $this->assertStringContainsString('2023-06-15 14:30:25.123456', $formatted);
        $this->assertStringContainsString('2023-06-15 18:45:30.654321', $formatted);
    }

    #[Test]
    public function can_handle_microseconds_in_formatting(): void
    {
        $tsRange = new TsRange(
            new \DateTimeImmutable('2023-01-01 10:00:00.123456'),
            new \DateTimeImmutable('2023-01-01 18:00:00.654321')
        );

        $this->assertEquals('[2023-01-01 10:00:00.123456,2023-01-01 18:00:00.654321)', (string) $tsRange);
    }

    #[Test]
    public function can_parse_timestamp_strings_via_from_string(): void
    {
        $tsRange = TsRange::fromString('[2023-06-15 14:30:25.123456,2023-06-15 18:45:30.654321)');

        $formatted = (string) $tsRange;
        $this->assertStringContainsString('2023-06-15 14:30:25.123456', $formatted);
        $this->assertStringContainsString('2023-06-15 18:45:30.654321', $formatted);
    }

    #[Test]
    public function throws_exception_for_invalid_timestamp_string_via_from_string(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid timestamp value');

        TsRange::fromString('[invalid_timestamp,2023-01-01 18:00:00)');
    }

    #[Test]
    public function can_handle_timezone_information_in_input(): void
    {
        // TsRange preserves the original timestamp but formats without timezone info
        $timestampWithTz = new \DateTimeImmutable('2023-01-01 10:00:00+02:00');
        $tsRange = new TsRange($timestampWithTz, null);

        // The timestamp should be formatted as-is without timezone conversion
        $formatted = (string) $tsRange;
        $this->assertStringContainsString('2023-01-01 10:00:00.000000', $formatted);
    }
}
