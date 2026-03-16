<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Interval;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IntervalTest extends TestCase
{
    #[DataProvider('provideParsingAndFormatting')]
    #[Test]
    public function can_parse_and_format(string $input, string $expectedOutput): void
    {
        $interval = Interval::fromString($input);

        $this->assertSame($expectedOutput, (string) $interval);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideParsingAndFormatting(): array
    {
        return [
            'postgres style: year' => ['1 year', '1 year'],
            'postgres style: years' => ['2 years', '2 years'],
            'postgres style: mon' => ['1 mon', '1 mon'],
            'postgres style: mons' => ['2 mons', '2 mons'],
            'postgres style: day' => ['1 day', '1 day'],
            'postgres style: days' => ['3 days', '3 days'],
            'postgres style: time only' => ['04:05:06', '04:05:06'],
            'postgres style: full' => ['1 year 2 mons 3 days 04:05:06', '1 year 2 mons 3 days 04:05:06'],
            'postgres style: zero' => ['00:00:00', '00:00:00'],
            'postgres style: negative time' => ['-04:05:06', '-04:05:06'],
            'postgres style: negative year' => ['-1 year', '-1 year'],
            'postgres style: fractional seconds' => ['00:00:01.5', '00:00:01.5'],
            'verbose: months' => ['2 months', '2 mons'],
            'verbose: month' => ['1 month', '1 mon'],
            'verbose: full' => ['1 year 2 months 3 days 4 hours 5 minutes 6 seconds', '1 year 2 mons 3 days 04:05:06'],
            'ISO 8601: year' => ['P1Y', '1 year'],
            'ISO 8601: full' => ['P1Y2M3DT4H5M6S', '1 year 2 mons 3 days 04:05:06'],
            'ISO 8601: time only' => ['PT4H5M6S', '04:05:06'],
            'ISO 8601: negative' => ['-P1Y2M', '-1 year -2 mons'],
            'postgres style: negative year with positive time' => ['-1 years +04:05:06', '-1 year +04:05:06'],
            'postgres style: negative days with positive time' => ['-3 days +02:00:00', '-3 days +02:00:00'],
            'postgres style: all negative' => ['-1 years -2 mons -3 days -04:05:06', '-1 year -2 mons -3 days -04:05:06'],
            'postgres style: large hours' => ['100:00:00', '100:00:00'],
            'postgres style: fractional seconds full precision' => ['00:00:01.123456', '00:00:01.123456'],
            'postgres style: days only' => ['30 days', '30 days'],
            'sql_standard: year-month' => ['1-2', '1 year 2 mons'],
            'sql_standard: year-month with days and time' => ['1-2 3 4:05:06', '1 year 2 mons 3 days 04:05:06'],
            'verbose: fractional seconds' => ['1.5 seconds', '00:00:01.5'],
        ];
    }

    #[Test]
    public function throws_exception_for_empty_string(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Interval::fromString('');
    }

    #[Test]
    public function can_create_from_date_interval(): void
    {
        $dateInterval = new \DateInterval('P1Y2M3DT4H5M6S');
        $interval = Interval::fromDateInterval($dateInterval);

        $this->assertSame('1 year 2 mons 3 days 04:05:06', (string) $interval);
    }

    #[Test]
    public function can_create_from_inverted_date_interval(): void
    {
        $dateInterval = new \DateInterval('P1Y');
        $dateInterval->invert = 1;

        $interval = Interval::fromDateInterval($dateInterval);

        $this->assertSame('-1 year', (string) $interval);
    }

    #[Test]
    public function can_convert_to_date_interval(): void
    {
        $interval = Interval::fromString('1 year 2 mons 3 days 04:05:06');
        $dateInterval = $interval->toDateInterval();

        $this->assertSame(1, $dateInterval->y);
        $this->assertSame(2, $dateInterval->m);
        $this->assertSame(3, $dateInterval->d);
        $this->assertSame(4, $dateInterval->h);
        $this->assertSame(5, $dateInterval->i);
        $this->assertSame(6, $dateInterval->s);
    }

    #[Test]
    public function to_date_interval_returns_clone(): void
    {
        $interval = Interval::fromString('1 year');

        $this->assertNotSame($interval->toDateInterval(), $interval->toDateInterval());
        $this->assertEquals($interval->toDateInterval(), $interval->toDateInterval());
    }
}
