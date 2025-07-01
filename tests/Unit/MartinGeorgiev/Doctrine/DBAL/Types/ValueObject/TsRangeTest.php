<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsRange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TsRangeTest extends TestCase
{
    #[Test]
    public function can_create_simple_range(): void
    {
        $start = new \DateTimeImmutable('2023-01-01 10:00:00');
        $end = new \DateTimeImmutable('2023-01-01 18:00:00');
        $tsRange = new TsRange($start, $end);

        self::assertEquals('[2023-01-01 10:00:00.000000,2023-01-01 18:00:00.000000)', (string) $tsRange);
        self::assertFalse($tsRange->isEmpty());
    }

    #[Test]
    public function can_create_empty_range(): void
    {
        $tsRange = TsRange::empty();

        self::assertEquals('empty', (string) $tsRange);
        self::assertTrue($tsRange->isEmpty());
    }

    #[Test]
    public function can_create_infinite_range(): void
    {
        $tsRange = TsRange::infinite();

        self::assertEquals('(,)', (string) $tsRange);
        self::assertFalse($tsRange->isEmpty());
    }

    #[Test]
    public function can_create_inclusive_range(): void
    {
        $start = new \DateTimeImmutable('2023-01-01 10:00:00');
        $end = new \DateTimeImmutable('2023-01-01 18:00:00');
        $tsRange = new TsRange($start, $end, true, true);

        self::assertEquals('[2023-01-01 10:00:00.000000,2023-01-01 18:00:00.000000]', (string) $tsRange);
        self::assertFalse($tsRange->isEmpty());
    }

    #[Test]
    public function can_create_hour_range(): void
    {
        $dateTime = new \DateTimeImmutable('2023-01-01 14:30:00');
        $tsRange = TsRange::hour($dateTime);

        self::assertEquals('[2023-01-01 14:00:00.000000,2023-01-01 15:00:00.000000)', (string) $tsRange);
        self::assertFalse($tsRange->isEmpty());
    }

    #[Test]
    #[DataProvider('providesContainsTestCases')]
    public function can_check_contains(TsRange $tsRange, mixed $value, bool $expected): void
    {
        self::assertEquals($expected, $tsRange->contains($value));
    }

    #[Test]
    #[DataProvider('providesFromStringTestCases')]
    public function can_parse_from_string(string $input, TsRange $tsRange): void
    {
        $result = TsRange::fromString($input);

        self::assertEquals($tsRange->__toString(), $result->__toString());
        self::assertEquals($tsRange->isEmpty(), $result->isEmpty());
    }

    #[Test]
    public function handles_microseconds_correctly(): void
    {
        $start = new \DateTimeImmutable('2023-01-01 10:00:00.123456');
        $end = new \DateTimeImmutable('2023-01-01 18:00:00.654321');
        $tsRange = new TsRange($start, $end);

        self::assertEquals('[2023-01-01 10:00:00.123456,2023-01-01 18:00:00.654321)', (string) $tsRange);
    }

    public static function providesContainsTestCases(): \Generator
    {
        $tsRange = new TsRange(
            new \DateTimeImmutable('2023-01-01 10:00:00'),
            new \DateTimeImmutable('2023-01-01 18:00:00')
        );

        yield 'contains timestamp in range' => [$tsRange, new \DateTimeImmutable('2023-01-01 14:00:00'), true];
        yield 'contains lower bound (inclusive)' => [$tsRange, new \DateTimeImmutable('2023-01-01 10:00:00'), true];
        yield 'does not contain upper bound (exclusive)' => [$tsRange, new \DateTimeImmutable('2023-01-01 18:00:00'), false];
        yield 'does not contain timestamp before range' => [$tsRange, new \DateTimeImmutable('2023-01-01 09:00:00'), false];
        yield 'does not contain timestamp after range' => [$tsRange, new \DateTimeImmutable('2023-01-01 19:00:00'), false];
        yield 'does not contain null' => [$tsRange, null, false];

        $emptyRange = TsRange::empty();
        yield 'empty range contains nothing' => [$emptyRange, new \DateTimeImmutable('2023-01-01 14:00:00'), false];
    }

    public static function providesFromStringTestCases(): \Generator
    {
        yield 'simple range' => [
            '[2023-01-01 10:00:00,2023-01-01 18:00:00)',
            new TsRange(new \DateTimeImmutable('2023-01-01 10:00:00'), new \DateTimeImmutable('2023-01-01 18:00:00')),
        ];
        yield 'inclusive range' => [
            '[2023-01-01 10:00:00,2023-01-01 18:00:00]',
            new TsRange(new \DateTimeImmutable('2023-01-01 10:00:00'), new \DateTimeImmutable('2023-01-01 18:00:00'), true, true),
        ];
        yield 'infinite lower' => [
            '[,2023-01-01 18:00:00)',
            new TsRange(null, new \DateTimeImmutable('2023-01-01 18:00:00')),
        ];
        yield 'infinite upper' => [
            '[2023-01-01 10:00:00,)',
            new TsRange(new \DateTimeImmutable('2023-01-01 10:00:00'), null),
        ];
        yield 'empty range' => ['empty', TsRange::empty()];
    }
}
