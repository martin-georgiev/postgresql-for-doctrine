<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidIntervalException;

/**
 * Value object representing a PostgreSQL interval value, backed by PHP's DateInterval.
 *
 * Reads every output format PostgreSQL can produce, i.e. all four IntervalStyle settings:
 * - postgres: 1 year 2 mons 3 days 04:05:06
 * - postgres_verbose: @ 1 year 2 mons 3 days 4 hours 5 mins 6 secs
 * - sql_standard: +1-2 +3 +4:05:06
 * - iso_8601: P1Y2M3DT4H5M6S
 *
 * Fractional units follow PostgreSQL's own propagation into the next lower unit, e.g.
 * "1.5 days" is 1 day 12:00:00 and "-1.5 days" is -1 days -12:00:00.
 *
 * @see https://www.postgresql.org/docs/18/datatype-datetime.html#DATATYPE-INTERVAL-INPUT
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @phpstan-consistent-constructor
 */
class Interval implements \Stringable
{
    private const MICROSECONDS_PER_SECOND = 1_000_000;

    private const MICROSECONDS_PER_MINUTE = 60_000_000;

    private const MICROSECONDS_PER_HOUR = 3_600_000_000;

    private const MICROSECONDS_PER_DAY = 86_400_000_000;

    private const MONTHS_PER_YEAR = 12;

    private const DAYS_PER_MONTH = 30;

    private const DAYS_PER_WEEK = 7;

    /**
     * @var array<string, string>
     */
    private const UNIT_ALIASES = [
        'y' => 'year',
        'yr' => 'year',
        'yrs' => 'year',
        'year' => 'year',
        'years' => 'year',
        'mon' => 'month',
        'mons' => 'month',
        'month' => 'month',
        'months' => 'month',
        'w' => 'week',
        'week' => 'week',
        'weeks' => 'week',
        'd' => 'day',
        'day' => 'day',
        'days' => 'day',
        'h' => 'hour',
        'hr' => 'hour',
        'hrs' => 'hour',
        'hour' => 'hour',
        'hours' => 'hour',
        'min' => 'minute',
        'mins' => 'minute',
        'minute' => 'minute',
        'minutes' => 'minute',
        's' => 'second',
        'sec' => 'second',
        'secs' => 'second',
        'second' => 'second',
        'seconds' => 'second',
    ];

    private const SQL_STANDARD_TIME_PATTERN = '([+-])?(\d+):(\d{2})(?::(\d{2})(?:\.(\d+))?)?';

    protected function __construct(
        private readonly \DateInterval $dateInterval,
    ) {}

    public function __toString(): string
    {
        return $this->formatForPostgres();
    }

    /**
     * @throws InvalidIntervalException if $value is an empty string or cannot be parsed
     */
    public static function fromString(string $value): static
    {
        if ('' === $value) {
            throw InvalidIntervalException::forEmptyValue($value);
        }

        return new static(self::parse($value));
    }

    public static function fromDateInterval(\DateInterval $dateInterval): static
    {
        return new static(self::cloneWithInvertApplied($dateInterval));
    }

    public function toDateInterval(): \DateInterval
    {
        return clone $this->dateInterval;
    }

    private static function parse(string $value): \DateInterval
    {
        $trimmed = \trim($value);

        if (\preg_match('/^[+-]?P/', $trimmed) === 1) {
            return self::createIntervalFromParts(self::parseIso8601($trimmed));
        }

        $parts = self::parseSqlStandardFormat($trimmed) ?? self::parseUnitBasedFormat($trimmed);

        return self::createIntervalFromParts($parts);
    }

    /**
     * @return array{int, int, int, int}
     */
    private static function parseIso8601(string $value): array
    {
        $body = $value;
        $invert = \str_starts_with($body, '-');
        if ($invert || \str_starts_with($body, '+')) {
            $body = \substr($body, 1);
        }

        $component = '([+-]?\d+(?:\.\d+)?)';
        $pattern = '/^P(?=.)'
            .'(?:'.$component.'Y)?(?:'.$component.'M)?(?:'.$component.'W)?(?:'.$component.'D)?'
            .'(?:T(?=.)(?:'.$component.'H)?(?:'.$component.'M)?(?:'.$component.'S)?)?\z/';

        if (\preg_match($pattern, $body, $matches) !== 1) {
            throw InvalidIntervalException::forInvalidIso8601Format($value);
        }

        $parts = [0, 0, 0, 0];
        foreach (['year', 'month', 'week', 'day', 'hour', 'minute', 'second'] as $index => $unit) {
            $amount = $matches[$index + 1] ?? '';
            if ($amount !== '') {
                $parts = self::applyUnit($parts, $unit, $amount);
            }
        }

        return $invert ? self::negate($parts) : $parts;
    }

    /**
     * PostgreSQL's sql_standard style lets a leading sign govern every field that carries
     * no sign of its own, so "-1-2 3 4:05:06" is -1 years -2 mons -3 days -04:05:06.
     *
     * @return array{int, int, int, int}|null null when $value is not in sql_standard shape
     */
    private static function parseSqlStandardFormat(string $value): ?array
    {
        $time = self::SQL_STANDARD_TIME_PATTERN;

        if (\preg_match('/^([+-])?(\d+)-(\d+)\s+([+-])?(\d+)\s+'.$time.'\z/', $value, $matches) === 1) {
            $leadingSign = self::signOf($matches[1], 1);

            return [
                $leadingSign * (int) $matches[2],
                $leadingSign * (int) $matches[3],
                self::signOf($matches[4], $leadingSign) * (int) $matches[5],
                self::signOf($matches[6], $leadingSign) * self::timeToMicroseconds($matches[7], $matches[8], $matches[9] ?? '', $matches[10] ?? ''),
            ];
        }

        if (\preg_match('/^([+-])?(\d+)-(\d+)\z/', $value, $matches) === 1) {
            $leadingSign = self::signOf($matches[1], 1);

            return [$leadingSign * (int) $matches[2], $leadingSign * (int) $matches[3], 0, 0];
        }

        if (\preg_match('/^([+-])?(\d+)\s+'.$time.'\z/', $value, $matches) === 1) {
            $leadingSign = self::signOf($matches[1], 1);

            return [
                0,
                0,
                $leadingSign * (int) $matches[2],
                self::signOf($matches[3], $leadingSign) * self::timeToMicroseconds($matches[4], $matches[5], $matches[6] ?? '', $matches[7] ?? ''),
            ];
        }

        return null;
    }

    /**
     * Covers the postgres and postgres_verbose styles plus PostgreSQL's traditional
     * unit-based input, all of which are sequences of signed amounts with unit names.
     *
     * @return array{int, int, int, int}
     */
    private static function parseUnitBasedFormat(string $value): array
    {
        $parts = [0, 0, 0, 0];
        $isNegated = false;
        $hasToken = false;
        $offset = 0;
        $length = \strlen($value);

        while ($offset < $length) {
            if (\in_array($value[$offset], [' ', "\t", ',', '@'], true)) {
                $offset++;

                continue;
            }

            // PostgreSQL takes `ago` only as the closing token of a value that already carries an
            // amount, so a leading, repeated or mid-value one is not a negation but malformed input.
            if (\preg_match('/ago(?![a-z])/Ai', $value, $matches, 0, $offset) === 1) {
                $remainder = \trim(\substr($value, $offset + 3), " \t,@");
                if (!$hasToken || $remainder !== '') {
                    throw InvalidIntervalException::forInvalidFormat($value);
                }

                $isNegated = true;
                $offset += 3;

                continue;
            }

            if (\preg_match('/([+-])?(\d+):(\d{2})(?::(\d{2})(?:\.(\d+))?)?(?![\d:.])/A', $value, $matches, 0, $offset) === 1) {
                $parts[3] += self::signOf($matches[1], 1) * self::timeToMicroseconds(
                    $matches[2],
                    $matches[3],
                    $matches[4] ?? '',
                    $matches[5] ?? ''
                );
                $offset += \strlen($matches[0]);
                $hasToken = true;

                continue;
            }

            if (\preg_match('/([+-]?\d+(?:\.\d+)?)\s*([a-z]+)/Ai', $value, $matches, 0, $offset) === 1) {
                $unit = self::UNIT_ALIASES[\strtolower($matches[2])] ?? null;
                if ($unit === null) {
                    throw InvalidIntervalException::forInvalidFormat($value);
                }

                $parts = self::applyUnit($parts, $unit, $matches[1]);
                $offset += \strlen($matches[0]);
                $hasToken = true;

                continue;
            }

            // A year-month field may also open a traditional value, as in '1-2 3 days'. Its
            // sign covers both of its own numbers but, unlike sql_standard, stops there
            if (\preg_match('/([+-])?(\d+)-(\d+)(?![\d.:-])/A', $value, $matches, 0, $offset) === 1) {
                $sign = self::signOf($matches[1], 1);
                $parts[0] += $sign * (int) $matches[2];
                $parts[1] += $sign * (int) $matches[3];
                $offset += \strlen($matches[0]);
                $hasToken = true;

                continue;
            }

            // PostgreSQL reads a unitless number as seconds, which is also how it writes zero
            if (\preg_match('/([+-]?\d+(?:\.\d+)?)(?![\d.])/A', $value, $matches, 0, $offset) === 1) {
                $parts = self::applyUnit($parts, 'second', $matches[1]);
                $offset += \strlen($matches[0]);
                $hasToken = true;

                continue;
            }

            throw InvalidIntervalException::forInvalidFormat($value);
        }

        if (!$hasToken) {
            throw InvalidIntervalException::forInvalidFormat($value);
        }

        return $isNegated ? self::negate($parts) : $parts;
    }

    /**
     * Fractional amounts spill into the next lower unit the way PostgreSQL does it:
     * a fractional year rounds to a whole month and stops there, while every other
     * fractional unit keeps cascading down to microseconds.
     *
     * @param array{int, int, int, int} $parts
     *
     * @return array{int, int, int, int}
     */
    private static function applyUnit(array $parts, string $unit, string $amount): array
    {
        $numeric = (float) $amount;

        switch ($unit) {
            case 'year':
                $totalMonths = (int) \round($numeric * self::MONTHS_PER_YEAR, 0, \PHP_ROUND_HALF_EVEN);
                $parts[0] += \intdiv($totalMonths, self::MONTHS_PER_YEAR);
                $parts[1] += $totalMonths % self::MONTHS_PER_YEAR;

                break;

            case 'month':
                $wholeMonths = (int) $numeric;
                $parts[1] += $wholeMonths;
                $parts = self::addDays($parts, ($numeric - $wholeMonths) * self::DAYS_PER_MONTH);

                break;

            case 'week':
                $parts = self::addDays($parts, $numeric * self::DAYS_PER_WEEK);

                break;

            case 'day':
                $parts = self::addDays($parts, $numeric);

                break;

            case 'hour':
                $parts[3] += (int) \round($numeric * self::MICROSECONDS_PER_HOUR);

                break;

            case 'minute':
                $parts[3] += (int) \round($numeric * self::MICROSECONDS_PER_MINUTE);

                break;

            default:
                $parts[3] += (int) \round($numeric * self::MICROSECONDS_PER_SECOND);

                break;
        }

        return $parts;
    }

    /**
     * @param array{int, int, int, int} $parts
     *
     * @return array{int, int, int, int}
     */
    private static function addDays(array $parts, float $days): array
    {
        $wholeDays = (int) $days;

        // Rounding the leftover to whole microseconds absorbs binary-float noise, so that
        // 0.4 months lands on 12 days rather than 11 days 23:59:59.999999
        $microseconds = (int) \round(($days - $wholeDays) * self::MICROSECONDS_PER_DAY);

        $parts[2] += $wholeDays + \intdiv($microseconds, self::MICROSECONDS_PER_DAY);
        $parts[3] += $microseconds % self::MICROSECONDS_PER_DAY;

        return $parts;
    }

    /**
     * @param array{int, int, int, int} $parts
     *
     * @return array{int, int, int, int}
     */
    private static function negate(array $parts): array
    {
        return [-$parts[0], -$parts[1], -$parts[2], -$parts[3]];
    }

    private static function signOf(string $sign, int $default): int
    {
        return match ($sign) {
            '-' => -1,
            '+' => 1,
            default => $default,
        };
    }

    private static function timeToMicroseconds(string $hours, string $minutes, string $seconds, string $fraction): int
    {
        $microseconds = (int) $hours * self::MICROSECONDS_PER_HOUR
            + (int) $minutes * self::MICROSECONDS_PER_MINUTE
            + (int) $seconds * self::MICROSECONDS_PER_SECOND;

        if ($fraction !== '') {
            $microseconds += (int) \str_pad(\substr($fraction, 0, 6), 6, '0');
        }

        return $microseconds;
    }

    /**
     * @param array{int, int, int, int} $parts
     */
    private static function createIntervalFromParts(array $parts): \DateInterval
    {
        [$years, $months, $days, $microseconds] = $parts;

        $sign = $microseconds < 0 ? -1 : 1;
        $remainder = \abs($microseconds);

        $hours = \intdiv($remainder, self::MICROSECONDS_PER_HOUR);
        $remainder %= self::MICROSECONDS_PER_HOUR;
        $minutes = \intdiv($remainder, self::MICROSECONDS_PER_MINUTE);
        $remainder %= self::MICROSECONDS_PER_MINUTE;
        $seconds = \intdiv($remainder, self::MICROSECONDS_PER_SECOND);
        $fraction = $remainder % self::MICROSECONDS_PER_SECOND;

        return self::createInterval(
            $years,
            $months,
            $days,
            $sign * $hours,
            $sign * $minutes,
            $sign * $seconds,
            (float) ($sign * $fraction) / self::MICROSECONDS_PER_SECOND
        );
    }

    private static function createInterval(int $years, int $months, int $days, int $hours, int $minutes, int $seconds, float $microseconds): \DateInterval
    {
        $dateInterval = new \DateInterval('PT0S');
        $dateInterval->y = $years;
        $dateInterval->m = $months;
        $dateInterval->d = $days;
        $dateInterval->h = $hours;
        $dateInterval->i = $minutes;
        $dateInterval->s = $seconds;
        $dateInterval->f = $microseconds;

        return $dateInterval;
    }

    private function formatForPostgres(): string
    {
        $parts = $this->formatDateParts();
        $timePart = $this->formatTimePart();

        if ($timePart !== null) {
            $parts[] = $timePart;
        }

        return $parts === [] ? '00:00:00' : \implode(' ', $parts);
    }

    /**
     * @return list<string>
     */
    private function formatDateParts(): array
    {
        $dateInterval = $this->dateInterval;
        $parts = [];

        if ($dateInterval->y !== 0) {
            $parts[] = $dateInterval->y.' year'.(\abs($dateInterval->y) !== 1 ? 's' : '');
        }

        if ($dateInterval->m !== 0) {
            $parts[] = $dateInterval->m.' mon'.(\abs($dateInterval->m) !== 1 ? 's' : '');
        }

        if ($dateInterval->d !== 0) {
            $parts[] = $dateInterval->d.' day'.(\abs($dateInterval->d) !== 1 ? 's' : '');
        }

        return $parts;
    }

    private function formatTimePart(): ?string
    {
        $dateInterval = $this->dateInterval;
        $hasMicroseconds = $dateInterval->f != 0;

        if ($dateInterval->h === 0 && $dateInterval->i === 0 && $dateInterval->s === 0 && !$hasMicroseconds) {
            return null;
        }

        $timeIsNegative = $dateInterval->h < 0 || $dateInterval->i < 0 || $dateInterval->s < 0 || $dateInterval->f < 0;
        $hasNegativeDateParts = $dateInterval->y < 0 || $dateInterval->m < 0 || $dateInterval->d < 0;
        $prefix = $timeIsNegative ? '-' : ($hasNegativeDateParts ? '+' : '');

        $result = \sprintf('%s%02d:%02d:%02d', $prefix, \abs($dateInterval->h), \abs($dateInterval->i), \abs($dateInterval->s));

        if ($hasMicroseconds) {
            $result .= '.'.\rtrim(\sprintf('%06d', (int) \round(\abs($dateInterval->f) * 1_000_000)), '0');
        }

        return $result;
    }

    private static function cloneWithInvertApplied(\DateInterval $dateInterval): \DateInterval
    {
        $sign = $dateInterval->invert ? -1 : 1;

        return self::createInterval(
            $sign * $dateInterval->y,
            $sign * $dateInterval->m,
            $sign * $dateInterval->d,
            $sign * $dateInterval->h,
            $sign * $dateInterval->i,
            $sign * $dateInterval->s,
            $sign * $dateInterval->f,
        );
    }
}
