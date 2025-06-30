<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

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

        self::assertEquals('[,]', (string) $dateRange);
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
}
