<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidRangeForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DateRangeTest extends TestCase
{
    #[Test]
    public function can_create_simple_range(): void
    {
        $start = new \DateTimeImmutable('2023-01-01');
        $end = new \DateTimeImmutable('2023-12-31');
        $dateRange = new DateRange($start, $end);

        self::assertEquals('[2023-01-01,2023-12-31)', (string) $dateRange);
        self::assertFalse($dateRange->isEmpty());
    }

    #[Test]
    public function can_create_empty_range(): void
    {
        $dateRange = DateRange::empty();

        self::assertEquals('empty', (string) $dateRange);
        self::assertTrue($dateRange->isEmpty());
    }

    #[Test]
    public function can_create_infinite_range(): void
    {
        $dateRange = DateRange::infinite();

        self::assertEquals('(,)', (string) $dateRange);
        self::assertFalse($dateRange->isEmpty());
    }

    #[Test]
    public function can_create_single_day_range(): void
    {
        $date = new \DateTimeImmutable('2023-06-15');
        $dateRange = DateRange::singleDay($date);

        self::assertEquals('[2023-06-15,2023-06-16)', (string) $dateRange);
        self::assertFalse($dateRange->isEmpty());
    }

    #[Test]
    public function can_create_year_range(): void
    {
        $dateRange = DateRange::year(2023);

        self::assertEquals('[2023-01-01,2024-01-01)', (string) $dateRange);
        self::assertFalse($dateRange->isEmpty());
    }

    #[Test]
    public function can_create_month_range(): void
    {
        $dateRange = DateRange::month(2023, 6);

        self::assertEquals('[2023-06-01,2023-07-01)', (string) $dateRange);
        self::assertFalse($dateRange->isEmpty());
    }

    #[Test]
    #[DataProvider('providesContainsTestCases')]
    public function can_check_contains(DateRange $dateRange, mixed $value, bool $expected): void
    {
        self::assertEquals($expected, $dateRange->contains($value));
    }

    #[Test]
    #[DataProvider('providesFromStringTestCases')]
    public function can_parse_from_string(string $input, DateRange $dateRange): void
    {
        $result = DateRange::fromString($input);

        self::assertEquals($dateRange->__toString(), $result->__toString());
        self::assertEquals($dateRange->isEmpty(), $result->isEmpty());
    }

    #[Test]
    public function throws_exception_for_invalid_lower_bound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Lower bound must be DateTimeInterface');

        new DateRange('invalid', new \DateTimeImmutable('2023-12-31'));
    }

    #[Test]
    public function throws_exception_for_invalid_upper_bound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Upper bound must be DateTimeInterface');

        new DateRange(new \DateTimeImmutable('2023-01-01'), 'invalid');
    }

    public static function providesContainsTestCases(): \Generator
    {
        $dateRange = new DateRange(
            new \DateTimeImmutable('2023-01-01'),
            new \DateTimeImmutable('2023-12-31')
        );

        yield 'contains date in range' => [$dateRange, new \DateTimeImmutable('2023-06-15'), true];
        yield 'contains lower bound (inclusive)' => [$dateRange, new \DateTimeImmutable('2023-01-01'), true];
        yield 'does not contain upper bound (exclusive)' => [$dateRange, new \DateTimeImmutable('2023-12-31'), false];
        yield 'does not contain date before range' => [$dateRange, new \DateTimeImmutable('2022-12-31'), false];
        yield 'does not contain date after range' => [$dateRange, new \DateTimeImmutable('2024-01-01'), false];
        yield 'does not contain null' => [$dateRange, null, false];

        $emptyRange = DateRange::empty();
        yield 'empty range contains nothing' => [$emptyRange, new \DateTimeImmutable('2023-06-15'), false];
    }

    public static function providesFromStringTestCases(): \Generator
    {
        yield 'simple range' => [
            '[2023-01-01,2023-12-31)',
            new DateRange(new \DateTimeImmutable('2023-01-01'), new \DateTimeImmutable('2023-12-31')),
        ];
        yield 'inclusive range' => [
            '[2023-01-01,2023-12-31]',
            new DateRange(new \DateTimeImmutable('2023-01-01'), new \DateTimeImmutable('2023-12-31'), true, true),
        ];
        yield 'infinite lower' => [
            '[,2023-12-31)',
            new DateRange(null, new \DateTimeImmutable('2023-12-31')),
        ];
        yield 'infinite upper' => [
            '[2023-01-01,)',
            new DateRange(new \DateTimeImmutable('2023-01-01'), null),
        ];
        yield 'empty range' => ['empty', DateRange::empty()];
    }

    #[Test]
    public function throws_exception_for_invalid_lower_bound_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Lower bound must be DateTimeInterface');

        new DateRange('invalid', new \DateTimeImmutable('2023-12-31'));
    }

    #[Test]
    public function throws_exception_for_invalid_upper_bound_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Upper bound must be DateTimeInterface');

        new DateRange(new \DateTimeImmutable('2023-01-01'), 'invalid');
    }

    #[Test]
    public function throws_exception_for_invalid_datetime_in_comparison_via_contains(): void
    {
        $dateRange = new DateRange(
            new \DateTimeImmutable('2023-01-01'),
            new \DateTimeImmutable('2023-12-31')
        );

        $this->expectException(InvalidRangeForPHPException::class);
        $this->expectExceptionMessage('Range bound must be a DateTimeInterface instance');

        $dateRange->contains('invalid');
    }

    #[Test]
    public function throws_exception_for_invalid_value_in_constructor(): void
    {
        $this->expectException(\TypeError::class);

        new DateRange('invalid', new \DateTimeImmutable('2023-12-31'));
    }

    #[Test]
    public function throws_exception_for_invalid_date_string_in_parse_via_from_string(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid range format');

        DateRange::fromString('[invalid_date,2023-12-31)');
    }

    #[Test]
    public function can_parse_various_date_formats_via_from_string(): void
    {
        $dateRange = DateRange::fromString('[2023-01-01,2023-12-31)');
        self::assertStringContainsString('2023-01-01', (string) $dateRange);

        $range2 = DateRange::fromString('[2023-12-31,2024-01-01)');
        self::assertStringContainsString('2023-12-31', (string) $range2);
    }

    #[Test]
    public function can_format_date_values_via_to_string(): void
    {
        // Time should be ignored in date formatting
        $dateRange = new DateRange(
            new \DateTimeImmutable('2023-06-15 14:30:00'),
            new \DateTimeImmutable('2023-06-16 20:45:00')
        );

        $formatted = (string) $dateRange;
        self::assertStringContainsString('2023-06-15', $formatted);
        self::assertStringContainsString('2023-06-16', $formatted);
        // Should not contain time information
        self::assertStringNotContainsString('14:30:00', $formatted);
        self::assertStringNotContainsString('20:45:00', $formatted);
    }

    #[Test]
    public function can_compare_dates_with_different_times_via_is_empty(): void
    {
        $date1 = new \DateTimeImmutable('2023-06-15 10:00:00');
        $date2 = new \DateTimeImmutable('2023-06-15 20:00:00');

        // Test comparison logic through isEmpty() - more natural than reflection
        // When lower > upper, range should be empty
        $reverseRange = new DateRange($date2, $date1); // 20:00 to 10:00
        self::assertTrue($reverseRange->isEmpty());

        // When lower < upper, range should not be empty
        $normalRange = new DateRange($date1, $date2); // 10:00 to 20:00
        self::assertFalse($normalRange->isEmpty());

        // When lower == upper with exclusive bounds, should be empty
        $equalExclusive = new DateRange($date1, $date1, false, false);
        self::assertTrue($equalExclusive->isEmpty());

        // When lower == upper with inclusive bounds, should not be empty
        $equalInclusive = new DateRange($date1, $date1, true, true);
        self::assertFalse($equalInclusive->isEmpty());
    }

    #[Test]
    #[DataProvider('provideLeapYearTestCases')]
    public function can_handle_leap_years(DateRange $dateRange, string $expectedString, string $description): void
    {
        self::assertEquals($expectedString, (string) $dateRange, $description);
    }

    #[Test]
    #[DataProvider('provideEdgeCaseMonthTestCases')]
    public function can_handle_edge_case_months(DateRange $dateRange, string $expectedString, string $description): void
    {
        self::assertEquals($expectedString, (string) $dateRange, $description);
    }

    public static function provideLeapYearTestCases(): \Generator
    {
        yield 'leap year 2024' => [
            DateRange::year(2024),
            '[2024-01-01,2025-01-01)',
            'Leap year should span full year correctly',
        ];
        yield 'leap year february 2024' => [
            DateRange::month(2024, 2),
            '[2024-02-01,2024-03-01)',
            'February in leap year should span correctly',
        ];
        yield 'non-leap year february 2023' => [
            DateRange::month(2023, 2),
            '[2023-02-01,2023-03-01)',
            'February in non-leap year should span correctly',
        ];
    }

    public static function provideEdgeCaseMonthTestCases(): \Generator
    {
        yield 'december crosses year boundary' => [
            DateRange::month(2023, 12),
            '[2023-12-01,2024-01-01)',
            'December should cross year boundary correctly',
        ];
        yield 'january starts year' => [
            DateRange::month(2023, 1),
            '[2023-01-01,2023-02-01)',
            'January should start year correctly',
        ];
    }
}
