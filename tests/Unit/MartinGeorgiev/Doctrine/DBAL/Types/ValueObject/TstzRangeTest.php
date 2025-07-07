<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzRange;
use PHPUnit\Framework\Attributes\Test;

final class TstzRangeTest extends BaseTimestampRangeTestCase
{
    protected function createSimpleRange(): Range
    {
        return new TstzRange(
            new \DateTimeImmutable('2023-01-01 10:00:00+00:00'),
            new \DateTimeImmutable('2023-01-01 18:00:00+00:00')
        );
    }

    protected function getExpectedSimpleRangeString(): string
    {
        return '[2023-01-01 10:00:00.000000+00:00,2023-01-01 18:00:00.000000+00:00)';
    }

    protected function createEmptyRange(): Range
    {
        return TstzRange::empty();
    }

    protected function createInfiniteRange(): Range
    {
        return TstzRange::infinite();
    }

    protected function createInclusiveRange(): Range
    {
        return new TstzRange(
            new \DateTimeImmutable('2023-01-01 10:00:00+00:00'),
            new \DateTimeImmutable('2023-01-01 18:00:00+00:00'),
            true,
            true
        );
    }

    protected function getExpectedInclusiveRangeString(): string
    {
        return '[2023-01-01 10:00:00.000000+00:00,2023-01-01 18:00:00.000000+00:00]';
    }

    protected function parseFromString(string $input): Range
    {
        return TstzRange::fromString($input);
    }

    protected function createBoundaryTestRange(): Range
    {
        return new TstzRange($this->getTestStartTime(), $this->getTestEndTime(), true, false);
    }

    protected function createRangeWithTimes(
        ?\DateTimeInterface $start,
        ?\DateTimeInterface $end,
        bool $lowerInclusive = true,
        bool $upperInclusive = false
    ): Range {
        return new TstzRange($start, $end, $lowerInclusive, $upperInclusive);
    }

    protected function getTestStartTime(): \DateTimeInterface
    {
        return new \DateTimeImmutable('2023-01-01 10:00:00+00:00');
    }

    protected function getTestEndTime(): \DateTimeInterface
    {
        return new \DateTimeImmutable('2023-01-01 18:00:00+00:00');
    }

    protected function getTimeBeforeRange(): \DateTimeInterface
    {
        return new \DateTimeImmutable('2023-01-01 09:00:00+00:00');
    }

    protected function getTimeInMiddle(): \DateTimeInterface
    {
        return new \DateTimeImmutable('2023-01-01 14:00:00+00:00');
    }

    protected function createTimeWithMicroseconds(string $timeString): \DateTimeInterface
    {
        return new \DateTimeImmutable($timeString);
    }

    protected function getTestStartTimeString(): string
    {
        return '2023-01-01 10:00:00+00:00';
    }

    protected function getTestEndTimeString(): string
    {
        return '2023-01-01 18:00:00+00:00';
    }

    #[Test]
    public function can_create_hour_range(): void
    {
        $start = new \DateTimeImmutable('2023-01-01 14:00:00+02:00');
        $end = $start->modify('+1 hour');
        $tstzRange = new TstzRange($start, $end);

        self::assertEquals('[2023-01-01 14:00:00.000000+02:00,2023-01-01 15:00:00.000000+02:00)', (string) $tstzRange);
        self::assertFalse($tstzRange->isEmpty());
    }

    public static function provideContainsTestCases(): \Generator
    {
        $start = new \DateTimeImmutable('2023-01-01 10:00:00+00:00');
        $end = new \DateTimeImmutable('2023-01-01 18:00:00+00:00');
        $tstzRange = new TstzRange($start, $end);

        yield 'contains value in range' => [
            $tstzRange,
            new \DateTimeImmutable('2023-01-01 14:00:00+00:00'),
            true,
        ];
        yield 'contains lower bound (inclusive)' => [$tstzRange, $start, true];
        yield 'does not contain upper bound (exclusive)' => [$tstzRange, $end, false];
        yield 'does not contain value before range' => [
            $tstzRange,
            new \DateTimeImmutable('2023-01-01 09:00:00+00:00'),
            false,
        ];
        yield 'does not contain null' => [$tstzRange, null, false];
    }

    public static function provideFromStringTestCases(): \Generator
    {
        yield 'simple range' => [
            '[2023-01-01 10:00:00.000000+00:00,2023-01-01 18:00:00.000000+00:00)',
            new TstzRange(
                new \DateTimeImmutable('2023-01-01 10:00:00+00:00'),
                new \DateTimeImmutable('2023-01-01 18:00:00+00:00')
            ),
        ];
        yield 'inclusive range' => [
            '[2023-01-01 10:00:00.000000+00:00,2023-01-01 18:00:00.000000+00:00]',
            new TstzRange(
                new \DateTimeImmutable('2023-01-01 10:00:00+00:00'),
                new \DateTimeImmutable('2023-01-01 18:00:00+00:00'),
                true,
                true
            ),
        ];
        yield 'empty range' => ['empty', TstzRange::empty()];
    }

    #[Test]
    public function handles_different_timezones(): void
    {
        $start = new \DateTimeImmutable('2023-01-01 10:00:00+02:00');
        $end = new \DateTimeImmutable('2023-01-01 18:00:00-05:00');
        $tstzRange = new TstzRange($start, $end);

        self::assertEquals('[2023-01-01 10:00:00.000000+02:00,2023-01-01 18:00:00.000000-05:00)', (string) $tstzRange);
    }

    #[Test]
    public function handles_microseconds_with_timezone(): void
    {
        $start = new \DateTimeImmutable('2023-01-01 10:00:00.123456+00:00');
        $end = new \DateTimeImmutable('2023-01-01 18:00:00.654321+00:00');
        $tstzRange = new TstzRange($start, $end);

        self::assertEquals('[2023-01-01 10:00:00.123456+00:00,2023-01-01 18:00:00.654321+00:00)', (string) $tstzRange);
    }

    public static function providesContainsTestCases(): \Generator
    {
        $tstzRange = new TstzRange(
            new \DateTimeImmutable('2023-01-01 10:00:00+00:00'),
            new \DateTimeImmutable('2023-01-01 18:00:00+00:00')
        );

        yield 'contains timestamp in range' => [$tstzRange, new \DateTimeImmutable('2023-01-01 14:00:00+00:00'), true];
        yield 'contains lower bound (inclusive)' => [$tstzRange, new \DateTimeImmutable('2023-01-01 10:00:00+00:00'), true];
        yield 'does not contain upper bound (exclusive)' => [$tstzRange, new \DateTimeImmutable('2023-01-01 18:00:00+00:00'), false];
        yield 'does not contain timestamp before range' => [$tstzRange, new \DateTimeImmutable('2023-01-01 09:00:00+00:00'), false];
        yield 'does not contain timestamp after range' => [$tstzRange, new \DateTimeImmutable('2023-01-01 19:00:00+00:00'), false];
        yield 'does not contain null' => [$tstzRange, null, false];

        $emptyRange = TstzRange::empty();
        yield 'empty range contains nothing' => [$emptyRange, new \DateTimeImmutable('2023-01-01 14:00:00+00:00'), false];
    }

    public static function providesFromStringTestCases(): \Generator
    {
        yield 'simple range with timezone' => [
            '[2023-01-01 10:00:00+00:00,2023-01-01 18:00:00+00:00)',
            new TstzRange(new \DateTimeImmutable('2023-01-01 10:00:00+00:00'), new \DateTimeImmutable('2023-01-01 18:00:00+00:00')),
        ];
        yield 'inclusive range with timezone' => [
            '[2023-01-01 10:00:00+02:00,2023-01-01 18:00:00+02:00]',
            new TstzRange(new \DateTimeImmutable('2023-01-01 10:00:00+02:00'), new \DateTimeImmutable('2023-01-01 18:00:00+02:00'), true, true),
        ];
        yield 'infinite lower' => [
            '[,2023-01-01 18:00:00+00:00)',
            new TstzRange(null, new \DateTimeImmutable('2023-01-01 18:00:00+00:00')),
        ];
        yield 'infinite upper' => [
            '[2023-01-01 10:00:00+00:00,)',
            new TstzRange(new \DateTimeImmutable('2023-01-01 10:00:00+00:00'), null),
        ];
        yield 'empty range' => ['empty', TstzRange::empty()];
    }

    #[Test]
    public function throws_exception_for_invalid_constructor_input(): void
    {
        $this->expectException(\TypeError::class);

        new TstzRange('invalid', new \DateTimeImmutable('2023-01-01 18:00:00+00:00'));
    }

    #[Test]
    public function can_format_timestamp_with_timezone_via_to_string(): void
    {
        $tstzRange = new TstzRange(
            new \DateTimeImmutable('2023-06-15 14:30:25.123456+02:00'),
            new \DateTimeImmutable('2023-06-15 18:45:30.654321+02:00')
        );

        $formatted = (string) $tstzRange;
        self::assertStringContainsString('2023-06-15 14:30:25.123456+02:00', $formatted);
        self::assertStringContainsString('2023-06-15 18:45:30.654321+02:00', $formatted);
    }

    #[Test]
    public function can_handle_timezone_comparison(): void
    {
        $utc = new \DateTimeImmutable('2023-01-01 10:00:00+00:00');
        $est = new \DateTimeImmutable('2023-01-01 05:00:00-05:00'); // Same moment as UTC
        $later = new \DateTimeImmutable('2023-01-01 15:00:00+00:00'); // Later moment

        $tstzRange = new TstzRange($utc, $later);

        // EST represents the same moment as the lower bound, so it should be contained (inclusive lower)
        self::assertTrue($tstzRange->contains($est));
    }

    #[Test]
    public function can_parse_timestamp_with_timezone_strings_via_from_string(): void
    {
        $tstzRange = TstzRange::fromString('[2023-06-15 14:30:25.123456+02:00,2023-06-15 18:45:30.654321+02:00)');

        $formatted = (string) $tstzRange;
        self::assertStringContainsString('2023-06-15 14:30:25.123456+02:00', $formatted);
        self::assertStringContainsString('2023-06-15 18:45:30.654321+02:00', $formatted);
    }

    #[Test]
    public function throws_exception_for_invalid_timestamp_string_via_from_string(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid timestamp value');

        TstzRange::fromString('[invalid_timestamp,2023-01-01 18:00:00+00:00)');
    }

    #[Test]
    public function preserves_timezone_information(): void
    {
        $timestampWithTz = new \DateTimeImmutable('2023-01-01 10:00:00+02:00');
        $tstzRange = new TstzRange($timestampWithTz, null);

        $formatted = (string) $tstzRange;
        self::assertStringContainsString('+02:00', $formatted);
        self::assertStringContainsString('2023-01-01 10:00:00.000000+02:00', $formatted);
    }

    #[Test]
    public function can_handle_different_datetime_implementations(): void
    {
        $dateTime = new \DateTimeImmutable('2023-01-01 10:00:00+02:00');
        $dateTimeImmutable = new \DateTimeImmutable('2023-01-01 18:00:00-05:00');

        $tstzRange = new TstzRange($dateTime, $dateTimeImmutable);
        $formatted = (string) $tstzRange;

        self::assertStringContainsString('+02:00', $formatted);
        self::assertStringContainsString('-05:00', $formatted);
    }
}
