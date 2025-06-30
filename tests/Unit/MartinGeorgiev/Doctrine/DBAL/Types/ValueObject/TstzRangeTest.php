<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzRange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TstzRangeTest extends TestCase
{
    #[Test]
    public function can_create_simple_range(): void
    {
        $start = new \DateTimeImmutable('2023-01-01 10:00:00+00:00');
        $end = new \DateTimeImmutable('2023-01-01 18:00:00+00:00');
        $tstzRange = new TstzRange($start, $end);

        self::assertEquals('[2023-01-01 10:00:00.000000+00:00,2023-01-01 18:00:00.000000+00:00)', (string) $tstzRange);
        self::assertFalse($tstzRange->isEmpty());
    }

    #[Test]
    public function can_create_empty_range(): void
    {
        $tstzRange = TstzRange::empty();

        self::assertEquals('empty', (string) $tstzRange);
        self::assertTrue($tstzRange->isEmpty());
    }

    #[Test]
    public function can_create_infinite_range(): void
    {
        $tstzRange = TstzRange::infinite();

        self::assertEquals('(,)', (string) $tstzRange);
        self::assertFalse($tstzRange->isEmpty());
    }

    #[Test]
    public function can_create_inclusive_range(): void
    {
        $start = new \DateTimeImmutable('2023-01-01 10:00:00+00:00');
        $end = new \DateTimeImmutable('2023-01-01 18:00:00+00:00');
        $tstzRange = TstzRange::inclusive($start, $end);

        self::assertEquals('[2023-01-01 10:00:00.000000+00:00,2023-01-01 18:00:00.000000+00:00]', (string) $tstzRange);
        self::assertFalse($tstzRange->isEmpty());
    }

    #[Test]
    public function can_create_hour_range(): void
    {
        $dateTime = new \DateTimeImmutable('2023-01-01 14:30:00+02:00');
        $tstzRange = TstzRange::hour($dateTime);

        self::assertEquals('[2023-01-01 14:00:00.000000+02:00,2023-01-01 15:00:00.000000+02:00)', (string) $tstzRange);
        self::assertFalse($tstzRange->isEmpty());
    }

    #[Test]
    #[DataProvider('providesContainsTestCases')]
    public function can_check_contains(TstzRange $tstzRange, mixed $value, bool $expected): void
    {
        self::assertEquals($expected, $tstzRange->contains($value));
    }

    #[Test]
    #[DataProvider('providesFromStringTestCases')]
    public function can_parse_from_string(string $input, TstzRange $tstzRange): void
    {
        $result = TstzRange::fromString($input);

        self::assertEquals($tstzRange->__toString(), $result->__toString());
        self::assertEquals($tstzRange->isEmpty(), $result->isEmpty());
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
}
