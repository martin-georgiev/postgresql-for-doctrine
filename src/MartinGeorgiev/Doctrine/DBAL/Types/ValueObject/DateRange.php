<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidRangeException;

/**
 * Represents a PostgreSQL date range.
 *
 * @extends Range<\DateTimeInterface>
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class DateRange extends Range
{
    public function __construct(
        mixed $lower,
        mixed $upper,
        bool $isLowerBracketInclusive = true,
        bool $isUpperBracketInclusive = false,
        bool $isExplicitlyEmpty = false,
        bool $isLowerBoundedInfinity = false,
        bool $isUpperBoundedInfinity = false,
    ) {
        if ($lower !== null && !$lower instanceof \DateTimeInterface) {
            throw InvalidRangeException::forInvalidBoundType(\DateTimeInterface::class, $lower);
        }

        if ($upper !== null && !$upper instanceof \DateTimeInterface) {
            throw InvalidRangeException::forInvalidBoundType(\DateTimeInterface::class, $upper);
        }

        parent::__construct($lower, $upper, $isLowerBracketInclusive, $isUpperBracketInclusive, $isExplicitlyEmpty, $isLowerBoundedInfinity, $isUpperBoundedInfinity);
    }

    protected function compareBounds(mixed $a, mixed $b): int
    {
        if (!$a instanceof \DateTimeInterface) {
            throw InvalidRangeException::forInvalidBoundType(\DateTimeInterface::class, $a);
        }

        if (!$b instanceof \DateTimeInterface) {
            throw InvalidRangeException::forInvalidBoundType(\DateTimeInterface::class, $b);
        }

        return $a->getTimestamp() <=> $b->getTimestamp();
    }

    protected function formatValue(mixed $value): string
    {
        if (!$value instanceof \DateTimeInterface) {
            throw InvalidRangeException::forInvalidBoundType(\DateTimeInterface::class, $value);
        }

        return $value->format('Y-m-d');
    }

    protected static function parseValue(string $value): ?\DateTimeImmutable
    {
        if (self::isInfinityString($value)) {
            return null;
        }

        try {
            return new \DateTimeImmutable($value);
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
