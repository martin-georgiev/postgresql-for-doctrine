<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * Base class for PostgreSQL timestamp range types.
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseTimestampRange extends Range
{
    public function __construct(
        ?\DateTimeInterface $lower,
        ?\DateTimeInterface $upper,
        bool $isLowerBracketInclusive = true,
        bool $isUpperBracketInclusive = false,
        bool $isExplicitlyEmpty = false,
    ) {
        parent::__construct($lower, $upper, $isLowerBracketInclusive, $isUpperBracketInclusive, $isExplicitlyEmpty);
    }

    /**
     * Uses PostgreSQL's explicit empty state rather than mathematical tricks.
     */
    public static function empty(): static
    {
        return new static(null, null, true, false, true);
    }

    public static function infinite(): static
    {
        return new static(null, null, false, false);
    }

    public static function inclusive(?\DateTimeInterface $lower, ?\DateTimeInterface $upper): static
    {
        return new static($lower, $upper, true, true);
    }

    public static function hour(\DateTimeInterface $dateTime): static
    {
        $start = \DateTimeImmutable::createFromInterface($dateTime)->setTime(
            (int) $dateTime->format('H'),
            0,
            0,
            0
        );
        $end = $start->modify('+1 hour');

        return new static($start, $end, true, false);
    }

    public static function fromString(string $rangeString): static
    {
        $rangeString = \trim($rangeString);

        if ($rangeString === parent::EMPTY_RANGE_STRING) {
            return static::empty();
        }

        if (!\preg_match('/^(\[|\()("?[^",]*"?),("?[^",]*"?)(\]|\))$/', $rangeString, $matches)) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid range format: %s', $rangeString)
            );
        }

        $isLowerBracketInclusive = $matches[1] === parent::BRACKET_LOWER_INCLUSIVE;
        $isUpperBracketInclusive = $matches[4] === parent::BRACKET_UPPER_INCLUSIVE;
        $lowerBoundValue = $matches[2] === '' ? null : static::parseTimestampValue(\trim($matches[2], '"'));
        $upperBoundValue = $matches[3] === '' ? null : static::parseTimestampValue(\trim($matches[3], '"'));

        return new static($lowerBoundValue, $upperBoundValue, $isLowerBracketInclusive, $isUpperBracketInclusive);
    }

    /**
     * Compare timestamps with microsecond precision.
     * PHP's getTimestamp() only returns seconds, so we need separate microsecond comparison.
     */
    protected function compareBounds(mixed $a, mixed $b): int
    {
        $timestampComparison = $a->getTimestamp() <=> $b->getTimestamp();

        if ($timestampComparison !== 0) {
            return $timestampComparison;
        }

        return (int) $a->format('u') <=> (int) $b->format('u');
    }

    protected function formatValue(mixed $value): string
    {
        if (!$value instanceof \DateTimeInterface) {
            throw new \InvalidArgumentException('Value must be a DateTimeInterface');
        }

        return $value->format('Y-m-d H:i:s.u');
    }

    protected static function parseTimestampValue(string $value): \DateTimeImmutable
    {
        try {
            return new \DateTimeImmutable($value);
        } catch (\Exception $exception) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid timestamp value: %s. Error: %s', $value, $exception->getMessage()),
                0,
                $exception
            );
        }
    }

    protected static function parseValue(string $value): \DateTimeImmutable
    {
        return static::parseTimestampValue($value);
    }
}
