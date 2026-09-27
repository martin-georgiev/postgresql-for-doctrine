<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\Traits\PostgresEraConversionTrait;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidRangeException;

/**
 * Represents a PostgreSQL date range.
 *
 * @extends Range<\DateTimeInterface>
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class DateRange extends Range
{
    use PostgresEraConversionTrait;

    public function __construct(
        mixed $lower,
        mixed $upper,
        bool $isLowerBracketInclusive = true,
        bool $isUpperBracketInclusive = false,
        bool $isExplicitlyEmpty = false,
        bool $isLowerBoundedInfinity = false,
        bool $isUpperBoundedInfinity = false,
        bool $isLowerInfinityNegative = true,
        bool $isUpperInfinityNegative = false,
    ) {
        if ($lower !== null && !$lower instanceof \DateTimeInterface) {
            throw InvalidRangeException::forInvalidBoundType(\DateTimeInterface::class, $lower, InvalidRangeException::LOWER_BOUND);
        }

        if ($upper !== null && !$upper instanceof \DateTimeInterface) {
            throw InvalidRangeException::forInvalidBoundType(\DateTimeInterface::class, $upper, InvalidRangeException::UPPER_BOUND);
        }

        parent::__construct($lower, $upper, $isLowerBracketInclusive, $isUpperBracketInclusive, $isExplicitlyEmpty, $isLowerBoundedInfinity, $isUpperBoundedInfinity, $isLowerInfinityNegative, $isUpperInfinityNegative);
    }

    protected function compareBounds(mixed $a, mixed $b): int
    {
        if (!$a instanceof \DateTimeInterface) {
            throw InvalidRangeException::forInvalidBoundType(\DateTimeInterface::class, $a);
        }

        if (!$b instanceof \DateTimeInterface) {
            throw InvalidRangeException::forInvalidBoundType(\DateTimeInterface::class, $b);
        }

        return $this->calendarDate($a) <=> $this->calendarDate($b);
    }

    protected function hasNoValueBetween(mixed $lower, mixed $upper): bool
    {
        \assert($lower instanceof \DateTimeInterface && $upper instanceof \DateTimeInterface);

        $nextDay = \DateTimeImmutable::createFromInterface($lower)->modify('+1 day');

        return $this->calendarDate($nextDay) === $this->calendarDate($upper);
    }

    /**
     * A daterange holds dates, so the time of day a bound carries does not order it, just as formatValue() drops it.
     *
     * @return array{int, int, int}
     */
    private function calendarDate(\DateTimeInterface $date): array
    {
        return [(int) $date->format('Y'), (int) $date->format('n'), (int) $date->format('j')];
    }

    protected function formatValue(mixed $value): string
    {
        if (!$value instanceof \DateTimeInterface) {
            throw InvalidRangeException::forInvalidBoundType(\DateTimeInterface::class, $value);
        }

        return self::formatInPostgresEra($value, 'Y-m-d');
    }

    protected static function parseValue(string $value): ?\DateTimeImmutable
    {
        if (self::isInfinityString($value)) {
            return null;
        }

        try {
            return new \DateTimeImmutable(self::signYearForPhpParser($value));
        } catch (\Exception $exception) {
            throw InvalidRangeException::forUnparsableBound($value, $exception);
        }
    }

    public static function singleDay(\DateTimeInterface $date): self
    {
        $startOfDay = \DateTimeImmutable::createFromInterface($date)->setTime(0, 0, 0);
        $endOfDay = $startOfDay->modify('+1 day');

        return new self($startOfDay, $endOfDay, true, false);
    }

    public static function year(int $year): self
    {
        $startOfYear = new \DateTimeImmutable(\sprintf('%d-01-01', $year));
        $endOfYear = $startOfYear->modify('+1 year');

        return new self($startOfYear, $endOfYear, true, false);
    }

    public static function month(int $year, int $month): self
    {
        $startOfMonth = new \DateTimeImmutable(\sprintf('%d-%02d-01', $year, $month));
        $endOfMonth = $startOfMonth->modify('+1 month');

        return new self($startOfMonth, $endOfMonth, true, false);
    }
}
