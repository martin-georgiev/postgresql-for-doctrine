<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidIntervalException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Interval;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class IntervalTest extends TestCase
{
    #[DataProvider('provideParsingAndFormatting')]
    #[Test]
    public function creates_from_string(string $input, string $expectedOutput): void
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

    #[DataProvider('provideFractionalUnits')]
    #[Test]
    public function parses_fractional_units(string $input, string $expectedOutput): void
    {
        $this->assertSame($expectedOutput, (string) Interval::fromString($input));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideFractionalUnits(): array
    {
        return [
            'negative fractional days' => ['-1.5 days', '-1 day -12:00:00'],
            'fractional days' => ['1.5 days', '1 day 12:00:00'],
            'fractional days below one' => ['-0.5 days', '-12:00:00'],
            'fractional days cascading into minutes' => ['1.05 days', '1 day 01:12:00'],
            'fractional hours' => ['1.5 hours', '01:30:00'],
            'fractional minutes' => ['1.5 min', '00:01:30'],
            'fractional seconds keep microseconds' => ['1.0005 secs', '00:00:01.0005'],
            'fractional years round to whole months' => ['2.7 years', '2 years 8 mons'],
            'fractional years round up' => ['2.9 years', '2 years 11 mons'],
            'fractional years carry into a whole year' => ['0.99 years', '1 year'],
            'fractional months become days' => ['1.4 mons', '1 mon 12 days'],
            'fractional months cascade into hours' => ['1.45 mons', '1 mon 13 days 12:00:00'],
            'negative fractional months' => ['-1.45 mons', '-1 mon -13 days -12:00:00'],
            'fractional weeks' => ['1.5 weeks', '10 days 12:00:00'],
            'fractional weeks below one' => ['0.1 weeks', '16:48:00'],
            'several fractional units combined' => ['1.5 days 1.5 hours', '1 day 13:30:00'],
        ];
    }

    #[DataProvider('provideSqlStandardOutput')]
    #[Test]
    public function parses_sql_standard_output(string $input, string $expectedOutput): void
    {
        $this->assertSame($expectedOutput, (string) Interval::fromString($input));
    }

    /**
     * Strings PostgreSQL emits under IntervalStyle sql_standard, where a leading sign governs
     * every field that carries no sign of its own.
     *
     * @return array<string, array{string, string}>
     */
    public static function provideSqlStandardOutput(): array
    {
        return [
            'negative year-month field' => ['-1-2', '-1 year -2 mons'],
            'negative year-month field with larger months' => ['-2-6', '-2 years -6 mons'],
            'days with zero time' => ['3 0:00:00', '3 days'],
            'week expressed as days' => ['7 0:00:00', '7 days'],
            'negative days with zero time' => ['-1 0:00:00', '-1 day'],
            'explicitly signed fields' => ['+0-10 +3 +0:00:00', '10 mons 3 days'],
            'all fields positive' => ['+1-2 +3 +4:05:06', '1 year 2 mons 3 days 04:05:06'],
            'leading sign governs unsigned fields' => ['-5 4:00:00', '-5 days -04:00:00'],
            'leading sign governs unsigned day and time' => ['-1 12:00:00', '-1 day -12:00:00'],
            'every field explicitly negative' => ['-1-2 -3 -4:05:06', '-1 year -2 mons -3 days -04:05:06'],
            'mixed field signs' => ['+0-0 +1 -2:03:04', '1 day -02:03:04'],
            'zero interval' => ['0', '00:00:00'],
            'unitless number is seconds' => ['7', '00:00:07'],
            'negative unitless number' => ['-7', '-00:00:07'],
            'time without leading zero' => ['1:30:00', '01:30:00'],
            'negative time without leading zero' => ['-0:00:01', '-00:00:01'],
        ];
    }

    #[DataProvider('provideYearMonthFieldWithTrailingValue')]
    #[Test]
    public function parses_year_month_field_opening_a_traditional_value(string $input, string $expectedOutput): void
    {
        $this->assertSame($expectedOutput, (string) Interval::fromString($input));
    }

    /**
     * Outside the strict sql_standard shapes a year-month field opens a traditional value, where
     * a trailing unitless number is seconds rather than days and the field's sign does not reach it.
     *
     * @return array<string, array{string, string}>
     */
    public static function provideYearMonthFieldWithTrailingValue(): array
    {
        return [
            'trailing number is seconds' => ['1-2 3', '1 year 2 mons 00:00:03'],
            'field sign does not reach the trailing number' => ['-1-2 3', '-1 year -2 mons +00:00:03'],
            'explicitly positive field' => ['+1-2 3', '1 year 2 mons 00:00:03'],
            'trailing number signed on its own' => ['-1-2 -3', '-1 year -2 mons -00:00:03'],
            'trailing days carry their unit' => ['1-2 3 days', '1 year 2 mons 3 days'],
            'trailing time field' => ['1-2 04:05:06', '1 year 2 mons 04:05:06'],
        ];
    }

    #[DataProvider('providePostgresVerboseOutput')]
    #[Test]
    public function parses_postgres_verbose_output(string $input, string $expectedOutput): void
    {
        $this->assertSame($expectedOutput, (string) Interval::fromString($input));
    }

    /**
     * Strings PostgreSQL emits under IntervalStyle postgres_verbose, which marks a negative
     * interval with a trailing 'ago' rather than per-field signs.
     *
     * @return array<string, array{string, string}>
     */
    public static function providePostgresVerboseOutput(): array
    {
        return [
            'abbreviated minutes' => ['@ 2 hours 30 mins', '02:30:00'],
            'minutes only' => ['@ 1 min', '00:01:00'],
            'abbreviated minutes and seconds' => ['@ 1 min 30 secs', '00:01:30'],
            'fractional seconds' => ['@ 4 hours 5 mins 6.5 secs', '04:05:06.5'],
            'zero interval' => ['@ 0', '00:00:00'],
            'days' => ['@ 3 days', '3 days'],
            'week expressed as days' => ['@ 7 days', '7 days'],
            'months and days' => ['@ 1 mon 15 days', '1 mon 15 days'],
            'ago negates the date parts' => ['@ 1 year 2 mons ago', '-1 year -2 mons'],
            'ago negates days and time' => ['@ 1 day 12 hours ago', '-1 day -12:00:00'],
            'ago negates a single second' => ['@ 1 sec ago', '-00:00:01'],
            'ago negates minutes and seconds' => ['@ 1 min 30 secs ago', '-00:01:30'],
            'ago negates days and hours' => ['@ 5 days 4 hours ago', '-5 days -04:00:00'],
            'ago negates every field' => ['@ 1 year 2 mons 3 days 4 hours 5 mins 6 secs ago', '-1 year -2 mons -3 days -04:05:06'],
            'per-field signs without ago' => ['@ 1 day -2 hours -3 mins -4 secs', '1 day -02:03:04'],
        ];
    }

    #[DataProvider('provideIso8601Output')]
    #[Test]
    public function parses_iso_8601_output(string $input, string $expectedOutput): void
    {
        $this->assertSame($expectedOutput, (string) Interval::fromString($input));
    }

    /**
     * Strings PostgreSQL emits under IntervalStyle iso_8601. \DateInterval rejects both the
     * per-component minus signs and the fractional seconds PostgreSQL writes here.
     *
     * @return array<string, array{string, string}>
     */
    public static function provideIso8601Output(): array
    {
        return [
            'negative day' => ['P-1D', '-1 day'],
            'negative year and month' => ['P-1Y-2M', '-1 year -2 mons'],
            'negative years' => ['P-2Y-6M', '-2 years -6 mons'],
            'negative day and hour' => ['P-5DT-4H', '-5 days -04:00:00'],
            'negative day with negative time' => ['P-1DT-12H', '-1 day -12:00:00'],
            'negative second' => ['PT-1S', '-00:00:01'],
            'every component negative' => ['P-1Y-2M-3DT-4H-5M-6S', '-1 year -2 mons -3 days -04:05:06'],
            'mixed component signs' => ['P1DT-2H-3M-4S', '1 day -02:03:04'],
            'fractional seconds' => ['PT4H5M6.5S', '04:05:06.5'],
            'zero interval' => ['PT0S', '00:00:00'],
            'week expressed as days' => ['P7D', '7 days'],
            'months and days' => ['P10M3D', '10 mons 3 days'],
            'day and hours' => ['P1DT12H', '1 day 12:00:00'],
            'minutes only' => ['PT1M', '00:01:00'],
            'minutes and seconds' => ['PT1M30S', '00:01:30'],
        ];
    }

    #[DataProvider('provideWeekUnits')]
    #[Test]
    public function parses_week_units(string $input, string $expectedOutput): void
    {
        $this->assertSame($expectedOutput, (string) Interval::fromString($input));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideWeekUnits(): array
    {
        return [
            'week' => ['1 week', '7 days'],
            'weeks' => ['2 weeks', '14 days'],
            'abbreviated week' => ['1 w', '7 days'],
            'negative week' => ['-1 week', '-7 days'],
            'week combined with days' => ['1 week 2 days', '9 days'],
        ];
    }

    #[DataProvider('provideOwnStringRepresentations')]
    #[Test]
    public function roundtrips_its_own_string_representation(string $representation): void
    {
        $interval = Interval::fromString($representation);

        $this->assertSame($representation, (string) $interval);
        $this->assertSame($representation, (string) Interval::fromString((string) $interval));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideOwnStringRepresentations(): array
    {
        return [
            'zero' => ['00:00:00'],
            'years and months' => ['1 year 2 mons'],
            'every field' => ['1 year 2 mons 3 days 04:05:06'],
            'negative day with negative time' => ['-1 day -12:00:00'],
            'negative days and hours' => ['-5 days -04:00:00'],
            'positive day with negative time' => ['1 day -02:03:04'],
            'negative date parts with positive time' => ['-1 year +04:05:06'],
            'every field negative' => ['-1 year -2 mons -3 days -04:05:06'],
            'fractional seconds' => ['04:05:06.5'],
            'full microsecond precision' => ['00:00:01.123456'],
            'hours beyond a day' => ['100:00:00'],
            'single negative second' => ['-00:00:01'],
        ];
    }

    #[DataProvider('provideExtendedUnitNames')]
    #[Test]
    public function parses_extended_unit_names(string $input, string $expectedOutput): void
    {
        $this->assertSame($expectedOutput, (string) Interval::fromString($input));
    }

    /**
     * Units PostgreSQL accepts on input but never writes back. Note that a bare 'm' is minutes,
     * and that a fractional microsecond is rounded half to even, so '1.5 us' is 1 and '2.5 us' is 2.
     *
     * @return array<string, array{string, string}>
     */
    public static function provideExtendedUnitNames(): array
    {
        return [
            'decade' => ['1 decade', '10 years'],
            'decades' => ['2 decades', '20 years'],
            'fractional decade' => ['0.15 decades', '1 year 6 mons'],
            'century' => ['1 century', '100 years'],
            'centuries' => ['2 centuries', '200 years'],
            'fractional century' => ['0.5 century', '50 years'],
            'millennium' => ['1 millennium', '1000 years'],
            'millennia' => ['2 millennia', '2000 years'],
            'millenniums' => ['1 millenniums', '1000 years'],
            'bare m is minutes' => ['1 m', '00:01:00'],
            'fractional bare m' => ['1.5 m', '00:01:30'],
            'millisecond' => ['1 millisecond', '00:00:00.001'],
            'milliseconds' => ['2 milliseconds', '00:00:00.002'],
            'abbreviated millisecond' => ['1 ms', '00:00:00.001'],
            'msec' => ['1 msec', '00:00:00.001'],
            'msecs' => ['1 msecs', '00:00:00.001'],
            'fractional millisecond' => ['1.5 ms', '00:00:00.0015'],
            'microsecond' => ['1 microsecond', '00:00:00.000001'],
            'microseconds' => ['2 microseconds', '00:00:00.000002'],
            'abbreviated microsecond' => ['1 us', '00:00:00.000001'],
            'usec' => ['1 usec', '00:00:00.000001'],
            'usecs' => ['1 usecs', '00:00:00.000001'],
            'microsecond tie rounds down to odd' => ['1.5 us', '00:00:00.000001'],
            'microsecond tie rounds down to even' => ['2.5 us', '00:00:00.000002'],
            'sub-microsecond remainder rounds up' => ['0.6 us', '00:00:00.000001'],
            'sub-microsecond remainder rounds down' => ['0.4 us', '00:00:00'],
            'larger units combined' => ['1 decade 1 century', '110 years'],
        ];
    }

    #[DataProvider('provideLooselyWrittenAmounts')]
    #[Test]
    public function parses_loosely_written_amounts(string $input, string $expectedOutput): void
    {
        $this->assertSame($expectedOutput, (string) Interval::fromString($input));
    }

    /**
     * PostgreSQL accepts single-digit minute and second fields and numbers written with a
     * dangling decimal point, none of which it ever writes back.
     *
     * @return array<string, array{string, string}>
     */
    public static function provideLooselyWrittenAmounts(): array
    {
        return [
            'single-digit minutes and seconds' => ['1:2:3', '01:02:03'],
            'single-digit minutes without seconds' => ['1:2', '01:02:00'],
            'single-digit seconds only' => ['1:02:3', '01:02:03'],
            'single-digit minutes only' => ['1:2:03', '01:02:03'],
            'negative single-digit fields' => ['-1:2:3', '-01:02:03'],
            'single-digit fields with a fraction' => ['1:2:3.5', '01:02:03.5'],
            'leading decimal point' => ['.5 days', '12:00:00'],
            'trailing decimal point' => ['5. days', '5 days'],
            'unitless leading decimal point' => ['.5', '00:00:00.5'],
            'unitless trailing decimal point' => ['5.', '00:00:05'],
        ];
    }

    #[DataProvider('provideSqlStandardValuesPostgresCannotRepresent')]
    #[Test]
    public function parses_sql_standard_output_the_way_postgres_reads_it_back(string $input, string $expectedOutput): void
    {
        $this->assertSame($expectedOutput, (string) Interval::fromString($input));
    }

    /**
     * The sql_standard style cannot express an interval whose fields differ in sign, so
     * PostgreSQL's own output is lossy here and reading it back changes the value. These cases
     * pin the parser to what PostgreSQL itself makes of those strings, not to the value that
     * was written: PostgreSQL turns '-1 mon 1 day 00:00:01' into '-0-1 -1 -0:00:01' and then
     * reads that back as '-1 mons -1 days -00:00:01'.
     *
     * @return array<string, array{string, string}>
     */
    public static function provideSqlStandardValuesPostgresCannotRepresent(): array
    {
        return [
            'months negative, days and time positive' => ['-0-1 -1 -0:00:01', '-1 mon -1 day -00:00:01'],
            'years borrowed into months' => ['-9999-11 -30 -23:59:59.999999', '-9999 years -11 mons -30 days -23:59:59.999999'],
        ];
    }

    #[DataProvider('provideInfiniteIntervals')]
    #[Test]
    public function throws_exception_for_infinite_intervals(string $value): void
    {
        $this->expectException(InvalidIntervalException::class);
        $this->expectExceptionMessage('Infinite intervals cannot be represented by DateInterval');

        Interval::fromString($value);
    }

    /**
     * PostgreSQL 17 and later can store an infinite interval, which DateInterval cannot carry.
     *
     * @return array<string, array{string}>
     */
    public static function provideInfiniteIntervals(): array
    {
        return [
            'positive' => ['infinity'],
            'negative' => ['-infinity'],
            'explicitly positive' => ['+infinity'],
            'mixed case' => ['Infinity'],
        ];
    }

    #[DataProvider('provideOutOfRangeAmounts')]
    #[Test]
    public function throws_exception_for_out_of_range_amounts(string $value): void
    {
        $this->expectException(InvalidIntervalException::class);
        $this->expectExceptionMessage('Interval amount is out of range');

        Interval::fromString($value);
    }

    /**
     * PostgreSQL keeps an interval in int64 microseconds and rejects anything wider. Without the
     * range guard PHP raises "float is not representable as int" and stores a nonsense value.
     *
     * @return array<string, array{string}>
     */
    public static function provideOutOfRangeAmounts(): array
    {
        return [
            'seconds' => ['9223372036854775807 secs'],
            'hours' => ['100000000000 hours'],
            'hours in a time field' => ['100000000000:00:00'],
            'days' => ['99999999999999999999999999 days'],
            'weeks' => ['99999999999999999999999999 weeks'],
            'months' => ['99999999999999999999999999 mons'],
            'years' => ['99999999999999999999999999 years'],
            'microseconds' => ['99999999999999999999999999 us'],
            'unitless' => ['99999999999999999999999999'],
        ];
    }

    #[Test]
    public function throws_exception_for_empty_string(): void
    {
        $this->expectException(InvalidIntervalException::class);
        Interval::fromString('');
    }

    #[DataProvider('provideMisplacedAgo')]
    #[Test]
    public function throws_exception_for_a_misplaced_ago(string $value): void
    {
        $this->expectException(InvalidIntervalException::class);

        Interval::fromString($value);
    }

    /**
     * PostgreSQL takes `ago` only as the closing token of a value that already carries an amount.
     *
     * @return array<string, array{string}>
     */
    public static function provideMisplacedAgo(): array
    {
        return [
            'leading' => ['ago 1 day'],
            'repeated' => ['1 day ago ago'],
            'followed by another amount' => ['1 day ago 2 hours'],
        ];
    }

    #[Test]
    public function throws_exception_for_invalid_iso_8601(): void
    {
        $this->expectException(InvalidIntervalException::class);
        $this->expectExceptionMessage('Invalid ISO 8601 interval string');
        Interval::fromString('Pinvalid');
    }

    #[Test]
    public function throws_exception_for_unrecognized_postgres_format(): void
    {
        $this->expectException(InvalidIntervalException::class);
        $this->expectExceptionMessage('Cannot parse interval string');
        Interval::fromString('not-an-interval');
    }

    #[DataProvider('provideUnparsableValues')]
    #[Test]
    public function throws_exception_for_unparsable_values(string $value): void
    {
        $this->expectException(InvalidIntervalException::class);
        Interval::fromString($value);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideUnparsableValues(): array
    {
        return [
            'unknown unit' => ['3 fortnights'],
            'ISO 8601 designator only' => ['P'],
            'ISO 8601 time designator only' => ['PT'],
            'verbose marker only' => ['@'],
            'bare words' => ['not-an-interval'],
        ];
    }

    #[Test]
    public function creates_from_date_interval(): void
    {
        $dateInterval = new \DateInterval('P1Y2M3DT4H5M6S');
        $interval = Interval::fromDateInterval($dateInterval);

        $this->assertSame('1 year 2 mons 3 days 04:05:06', (string) $interval);
    }

    #[Test]
    public function creates_from_inverted_date_interval(): void
    {
        $dateInterval = new \DateInterval('P1Y');
        $dateInterval->invert = 1;

        $interval = Interval::fromDateInterval($dateInterval);

        $this->assertSame('-1 year', (string) $interval);
    }

    #[DataProvider('provideUnnormalizedDateIntervals')]
    #[Test]
    public function normalizes_date_interval_fields_beyond_their_own_range(\DateInterval $dateInterval, string $expectedOutput): void
    {
        $this->assertSame($expectedOutput, (string) Interval::fromDateInterval($dateInterval));
    }

    /**
     * DateInterval accepts whatever is assigned to it without normalizing, so a fraction of a
     * second of one or more used to be printed as the fraction itself: f=1.5 gave '00:00:00.15'.
     *
     * @return \Generator<string, array{\DateInterval, string}>
     */
    public static function provideUnnormalizedDateIntervals(): \Generator
    {
        $wholeSecondAsFraction = new \DateInterval('PT0S');
        $wholeSecondAsFraction->f = 1.0;
        yield 'a whole second held as a fraction' => [$wholeSecondAsFraction, '00:00:01'];

        $fractionBeyondASecond = new \DateInterval('PT0S');
        $fractionBeyondASecond->f = 1.5;
        yield 'a fraction beyond a second' => [$fractionBeyondASecond, '00:00:01.5'];

        $negativeFractionBeyondASecond = new \DateInterval('PT0S');
        $negativeFractionBeyondASecond->f = -1.5;
        yield 'a negative fraction beyond a second' => [$negativeFractionBeyondASecond, '-00:00:01.5'];

        $fractionCarryingIntoMinutes = new \DateInterval('PT59S');
        $fractionCarryingIntoMinutes->f = 1.5;
        yield 'a fraction carrying into the next minute' => [$fractionCarryingIntoMinutes, '00:01:00.5'];

        yield 'seconds beyond a minute' => [new \DateInterval('PT90S'), '00:01:30'];
        yield 'minutes beyond an hour' => [new \DateInterval('PT1H90M'), '02:30:00'];
    }

    #[Test]
    public function converts_to_date_interval(): void
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
    public function returns_independent_date_interval_clones(): void
    {
        $interval = Interval::fromString('1 year');

        $this->assertNotSame($interval->toDateInterval(), $interval->toDateInterval());
        $this->assertEquals($interval->toDateInterval(), $interval->toDateInterval());
    }
}
