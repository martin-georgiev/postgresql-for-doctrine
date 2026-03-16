<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * Value object representing a PostgreSQL interval value, backed by PHP's DateInterval.
 *
 * Accepts any valid PostgreSQL interval string format:
 * - ISO 8601: P1Y2M3DT4H5M6S
 * - Verbose: 1 year 2 months 3 days 4 hours 5 minutes 6 seconds
 * - PostgreSQL output: 1 year 2 mons 3 days 04:05:06
 *
 * @see https://www.postgresql.org/docs/current/datatype-datetime.html#DATATYPE-INTERVAL-INPUT
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @phpstan-consistent-constructor
 */
class Interval implements \Stringable
{
    protected function __construct(
        private readonly \DateInterval $dateInterval,
    ) {}

    public function __toString(): string
    {
        return $this->formatForPostgres($this->dateInterval);
    }

    /**
     * @throws \InvalidArgumentException if $value is an empty string or cannot be parsed
     */
    public static function fromString(string $value): static
    {
        if ('' === $value) {
            throw new \InvalidArgumentException('Interval value must be a non-empty string');
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
        if (\str_starts_with($value, 'P') || \str_starts_with($value, '-P')) {
            return self::parseIso8601($value);
        }

        return self::parsePostgresFormat($value);
    }

    private static function parseIso8601(string $value): \DateInterval
    {
        $invert = false;
        if (\str_starts_with($value, '-')) {
            $invert = true;
            $value = \substr($value, 1);
        }

        $dateInterval = new \DateInterval($value);
        if ($invert) {
            $dateInterval->y *= -1;
            $dateInterval->m *= -1;
            $dateInterval->d *= -1;
            $dateInterval->h *= -1;
            $dateInterval->i *= -1;
            $dateInterval->s *= -1;
            $dateInterval->f *= -1;
        }

        return $dateInterval;
    }

    private static function parsePostgresFormat(string $value): \DateInterval
    {
        $years = 0;
        $months = 0;
        $days = 0;
        $hours = 0;
        $minutes = 0;
        $seconds = 0;
        $microseconds = 0.0;

        // sql_standard format: "Y-M [D [H:M:S]]" (e.g., "1-2", "1-2 3 4:05:06")
        if (\preg_match('/^(-?\d+)-(\d+)(?:\s+(-?\d+))?/', $value, $m)) {
            $years = (int) $m[1];
            $months = (int) $m[2];
            if (isset($m[3])) {
                $days = (int) $m[3];
            }
        } else {
            if (\preg_match('/(-?\d+)\s+years?/i', $value, $m)) {
                $years = (int) $m[1];
            }

            if (\preg_match('/(-?\d+)\s+mons?(?:ths?)?/i', $value, $m)) {
                $months = (int) $m[1];
            }

            if (\preg_match('/(-?\d+)\s+days?/i', $value, $m)) {
                $days = (int) $m[1];
            }
        }

        if (\preg_match('/(-?)(\d{1,2}):(\d{2}):(\d{2})(?:\.(\d+))?/', $value, $m)) {
            $sign = $m[1] === '-' ? -1 : 1;
            $hours = $sign * (int) $m[2];
            $minutes = $sign * (int) $m[3];
            $seconds = $sign * (int) $m[4];
            if (isset($m[5])) {
                $microseconds = $sign * (int) \str_pad(\substr($m[5], 0, 6), 6, '0') / 1_000_000;
            }
        } else {
            if (\preg_match('/(-?\d+)\s+hours?/i', $value, $m)) {
                $hours = (int) $m[1];
            }

            if (\preg_match('/(-?\d+)\s+minutes?/i', $value, $m)) {
                $minutes = (int) $m[1];
            }

            if (\preg_match('/(-?\d+(?:\.\d+)?)\s+seconds?/i', $value, $m)) {
                $secondsFloat = (float) $m[1];
                $seconds = (int) $secondsFloat;
                $microseconds = $secondsFloat - $seconds;
            }
        }

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

    /**
     * Formats a DateInterval in PostgreSQL's default output style.
     *
     * Matches PostgreSQL's "postgres" intervalstyle output:
     * - "1 year 2 mons 3 days 04:05:06"
     * - "00:00:00" for zero interval
     */
    private function formatForPostgres(\DateInterval $dateInterval): string
    {
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

        if ($dateInterval->h !== 0 || $dateInterval->i !== 0 || $dateInterval->s !== 0 || $dateInterval->f != 0) {
            $negative = $dateInterval->h < 0 || $dateInterval->i < 0 || $dateInterval->s < 0 || $dateInterval->f < 0;
            $timeStr = \sprintf(
                '%s%02d:%02d:%02d',
                $negative ? '-' : '',
                \abs($dateInterval->h),
                \abs($dateInterval->i),
                \abs($dateInterval->s),
            );

            if ($dateInterval->f != 0) {
                $frac = \rtrim(\sprintf('%06d', (int) \round(\abs($dateInterval->f) * 1_000_000)), '0');
                $timeStr .= '.'.$frac;
            }

            $parts[] = $timeStr;
        }

        if ($parts === []) {
            return '00:00:00';
        }

        return \implode(' ', $parts);
    }

    private static function cloneWithInvertApplied(\DateInterval $dateInterval): \DateInterval
    {
        $interval = new \DateInterval('PT0S');
        $multiplier = $dateInterval->invert ? -1 : 1;
        $interval->y = $multiplier * $dateInterval->y;
        $interval->m = $multiplier * $dateInterval->m;
        $interval->d = $multiplier * $dateInterval->d;
        $interval->h = $multiplier * $dateInterval->h;
        $interval->i = $multiplier * $dateInterval->i;
        $interval->s = $multiplier * $dateInterval->s;
        $interval->f = $multiplier * $dateInterval->f;

        return $interval;
    }
}
