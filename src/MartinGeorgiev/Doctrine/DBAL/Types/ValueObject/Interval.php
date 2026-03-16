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
 * @see https://www.postgresql.org/docs/18/datatype-datetime.html#DATATYPE-INTERVAL-INPUT
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

        try {
            $dateInterval = new \DateInterval($value);
        } catch (\Exception $exception) {
            throw new \InvalidArgumentException(\sprintf('Invalid ISO 8601 interval string: %s', $value), 0, $exception);
        }

        if ($invert) {
            $dateInterval->invert = 1;
        }

        return self::cloneWithInvertApplied($dateInterval);
    }

    private static function parsePostgresFormat(string $value): \DateInterval
    {
        [$years, $months, $days] = self::parseDateParts($value);
        [$hours, $minutes, $seconds, $microseconds] = self::parseTimeParts($value);

        return self::createInterval($years, $months, $days, $hours, $minutes, $seconds, $microseconds);
    }

    /**
     * @return array{int, int, int}
     */
    private static function parseDateParts(string $value): array
    {
        // sql_standard format: "Y-M [D]" (e.g., "1-2", "1-2 3 4:05:06")
        if (\preg_match('/^(-?\d+)-(\d+)(?:\s+(-?\d+))?/', $value, $m)) {
            return [(int) $m[1], (int) $m[2], isset($m[3]) ? (int) $m[3] : 0];
        }

        $years = \preg_match('/(-?\d+)\s+years?/i', $value, $m) ? (int) $m[1] : 0;
        $months = \preg_match('/(-?\d+)\s+mons?(?:ths?)?/i', $value, $m) ? (int) $m[1] : 0;
        $days = \preg_match('/(-?\d+)\s+days?/i', $value, $m) ? (int) $m[1] : 0;

        return [$years, $months, $days];
    }

    /**
     * @return array{int, int, int, float}
     */
    private static function parseTimeParts(string $value): array
    {
        // HH:MM:SS[.fraction] format with optional +/- sign
        if (\preg_match('/([+-]?)(\d+):(\d{2}):(\d{2})(?:\.(\d+))?/', $value, $m)) {
            $sign = $m[1] === '-' ? -1 : 1;
            $microseconds = isset($m[5])
                ? $sign * (int) \str_pad(\substr($m[5], 0, 6), 6, '0') / 1_000_000
                : 0.0;

            return [$sign * (int) $m[2], $sign * (int) $m[3], $sign * (int) $m[4], $microseconds];
        }

        // Verbose: "N hours N minutes N seconds"
        $hours = \preg_match('/(-?\d+)\s+hours?/i', $value, $m) ? (int) $m[1] : 0;
        $minutes = \preg_match('/(-?\d+)\s+minutes?/i', $value, $m) ? (int) $m[1] : 0;
        $seconds = 0;
        $microseconds = 0.0;

        if (\preg_match('/(-?\d+(?:\.\d+)?)\s+seconds?/i', $value, $m)) {
            $secondsFloat = (float) $m[1];
            $seconds = (int) $secondsFloat;
            $microseconds = $secondsFloat - $seconds;
        }

        return [$hours, $minutes, $seconds, $microseconds];
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

    private function formatForPostgres(\DateInterval $dateInterval): string
    {
        $parts = $this->formatDateParts($dateInterval);
        $timePart = $this->formatTimePart($dateInterval);

        if ($timePart !== null) {
            $parts[] = $timePart;
        }

        return $parts === [] ? '00:00:00' : \implode(' ', $parts);
    }

    /**
     * @return list<string>
     */
    private function formatDateParts(\DateInterval $dateInterval): array
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

        return $parts;
    }

    private function formatTimePart(\DateInterval $dateInterval): ?string
    {
        if ($dateInterval->h === 0 && $dateInterval->i === 0 && $dateInterval->s === 0 && $dateInterval->f == 0) {
            return null;
        }

        $timeIsNegative = $dateInterval->h < 0 || $dateInterval->i < 0 || $dateInterval->s < 0 || $dateInterval->f < 0;
        $hasNegativeDateParts = $dateInterval->y < 0 || $dateInterval->m < 0 || $dateInterval->d < 0;
        $prefix = $timeIsNegative ? '-' : ($hasNegativeDateParts ? '+' : '');

        $result = \sprintf('%s%02d:%02d:%02d', $prefix, \abs($dateInterval->h), \abs($dateInterval->i), \abs($dateInterval->s));

        if ($dateInterval->f != 0) {
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
