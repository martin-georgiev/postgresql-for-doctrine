<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidRangeForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range;
use PHPUnit\Framework\Attributes\Test;

/**
 * Base test case for timestamp range types (TsRange, TstzRange).
 * Provides common timestamp-specific test patterns.
 */
/**
 * @template R of \DateTimeInterface
 */
abstract class BaseTimestampRangeTestCase extends BaseRangeTestCase
{
    protected function getBoundaryTestCases(): array
    {
        return [
            'contains lower bound (inclusive)' => [
                'value' => $this->getTestStartTime(),
                'expected' => true,
            ],
            'does not contain value before range' => [
                'value' => $this->getTimeBeforeRange(),
                'expected' => false,
            ],
            'does not contain upper bound (exclusive)' => [
                'value' => $this->getTestEndTime(),
                'expected' => false,
            ],
            'contains value in middle' => [
                'value' => $this->getTimeInMiddle(),
                'expected' => true,
            ],
        ];
    }

    /**
     * @return array<string, array{range: Range<R>, expectedEmpty: bool}>
     */
    protected function getComparisonTestCases(): array
    {
        return [
            'reverse range should be empty' => [
                'range' => $this->createRangeWithTimes($this->getTestEndTime(), $this->getTestStartTime()),
                'expectedEmpty' => true,
            ],
            'normal range should not be empty' => [
                'range' => $this->createRangeWithTimes($this->getTestStartTime(), $this->getTestEndTime()),
                'expectedEmpty' => false,
            ],
            'equal bounds exclusive should be empty' => [
                'range' => $this->createRangeWithTimes(
                    $this->getTestStartTime(),
                    $this->getTestStartTime(),
                    false,
                    false
                ),
                'expectedEmpty' => true,
            ],
            'equal bounds inclusive should not be empty' => [
                'range' => $this->createRangeWithTimes(
                    $this->getTestStartTime(),
                    $this->getTestStartTime(),
                    true,
                    true
                ),
                'expectedEmpty' => false,
            ],
        ];
    }

    #[Test]
    public function throws_exception_for_invalid_constructor_input(): void
    {
        $this->expectException(\TypeError::class);

        /* @phpstan-ignore-next-line */
        $this->createRangeWithTimes('invalid', $this->getTestEndTime());
    }

    #[Test]
    public function throws_exception_for_invalid_parse_input(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid timestamp value');

        $this->parseFromString('[invalid_timestamp,2023-01-01 18:00:00)');
    }

    #[Test]
    public function throws_exception_for_invalid_contains_input(): void
    {
        $range = $this->createBoundaryTestRange();

        $this->expectException(InvalidRangeForPHPException::class);
        $this->expectExceptionMessage('Range bound must be a DateTimeInterface instance');

        $range->contains('invalid');
    }

    #[Test]
    public function can_handle_microsecond_precision(): void
    {
        $earlier = $this->createTimeWithMicroseconds('2023-01-01 10:00:00.123456');
        $later = $this->createTimeWithMicroseconds('2023-01-01 10:00:00.654321');

        $range = $this->createRangeWithTimes($earlier, $later);

        self::assertTrue($range->contains($this->createTimeWithMicroseconds('2023-01-01 10:00:00.400000')));
        self::assertFalse($range->contains($this->createTimeWithMicroseconds('2023-01-01 10:00:00.100000')));
        self::assertFalse($range->contains($this->createTimeWithMicroseconds('2023-01-01 10:00:00.700000')));
    }

    #[Test]
    public function can_handle_different_datetime_implementations(): void
    {
        $dateTime = new \DateTimeImmutable($this->getTestStartTimeString());
        $dateTimeImmutable = new \DateTimeImmutable($this->getTestEndTimeString());

        $range = $this->createRangeWithTimes($dateTime, $dateTimeImmutable);
        $formatted = (string) $range;

        self::assertStringContainsString('2023-01-01 10:00:00', $formatted);
        self::assertStringContainsString('2023-01-01 18:00:00', $formatted);
    }

    /**
     * Create a range with specific DateTimeInterface objects.
     *
     * @return Range<R>
     */
    abstract protected function createRangeWithTimes(
        ?\DateTimeInterface $start,
        ?\DateTimeInterface $end,
        bool $lowerInclusive = true,
        bool $upperInclusive = false
    ): Range;

    /**
     * Get test start time.
     */
    abstract protected function getTestStartTime(): \DateTimeInterface;

    /**
     * Get test end time.
     */
    abstract protected function getTestEndTime(): \DateTimeInterface;

    /**
     * Get time before the test range.
     */
    abstract protected function getTimeBeforeRange(): \DateTimeInterface;

    /**
     * Get time in the middle of the test range.
     */
    abstract protected function getTimeInMiddle(): \DateTimeInterface;

    /**
     * Create time with microseconds for precision testing.
     */
    abstract protected function createTimeWithMicroseconds(string $timeString): \DateTimeInterface;

    /**
     * Get test start time as string.
     */
    abstract protected function getTestStartTimeString(): string;

    /**
     * Get test end time as string.
     */
    abstract protected function getTestEndTimeString(): string;
}
