<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * @template R of int|float|\DateTimeInterface
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class Range implements \Stringable
{
    protected const BRACKET_LOWER_INCLUSIVE = '[';

    protected const BRACKET_LOWER_EXCLUSIVE = '(';

    protected const BRACKET_UPPER_INCLUSIVE = ']';

    protected const BRACKET_UPPER_EXCLUSIVE = ')';

    protected const EMPTY_RANGE_STRING = 'empty';

    /**
     * @param R|null $lower
     * @param R|null $upper
     * @param bool $isLowerBoundedInfinity For types supporting infinity (timestamps, dates, numeric), indicates lower bound is explicitly infinity
     * @param bool $isUpperBoundedInfinity For types supporting infinity (timestamps, dates, numeric), indicates upper bound is explicitly infinity
     */
    public function __construct(
        protected readonly mixed $lower,
        protected readonly mixed $upper,
        protected readonly bool $isLowerBracketInclusive = true,
        protected readonly bool $isUpperBracketInclusive = false,
        protected readonly bool $isExplicitlyEmpty = false,
        protected readonly bool $isLowerBoundedInfinity = false,
        protected readonly bool $isUpperBoundedInfinity = false,
    ) {}

    public function __toString(): string
    {
        if ($this->isEmpty()) {
            return self::EMPTY_RANGE_STRING;
        }

        $lowerBracket = $this->isLowerBracketInclusive ? self::BRACKET_LOWER_INCLUSIVE : self::BRACKET_LOWER_EXCLUSIVE;
        $upperBracket = $this->isUpperBracketInclusive ? self::BRACKET_UPPER_INCLUSIVE : self::BRACKET_UPPER_EXCLUSIVE;

        $formattedLowerBound = $this->isLowerBoundedInfinity ? '-infinity' : ($this->lower === null ? '' : $this->formatValue($this->lower));
        $formattedUpperBound = $this->isUpperBoundedInfinity ? 'infinity' : ($this->upper === null ? '' : $this->formatValue($this->upper));

        return $lowerBracket.$formattedLowerBound.','.$formattedUpperBound.$upperBracket;
    }

    /**
     * Following PostgreSQL's design philosophy, a range can be empty in two ways:
     * 1. Explicitly marked as empty (isExplicitlyEmpty flag = true)
     * 2. Mathematically empty due to bounds (lower > upper, or equal bounds with exclusive brackets)
     */
    public function isEmpty(): bool
    {
        if ($this->isExplicitlyEmpty) {
            return true;
        }

        if ($this->lower === null || $this->upper === null) {
            return false;
        }

        $comparison = $this->compareBounds($this->lower, $this->upper);
        if ($comparison > 0) {
            return true;
        }

        return $comparison === 0 && (!$this->isLowerBracketInclusive || !$this->isUpperBracketInclusive);
    }

    abstract protected function compareBounds(mixed $a, mixed $b): int;

    abstract protected function formatValue(mixed $value): string;

    protected static function isInfinityString(string $value): bool
    {
        $normalized = \strtolower($value);
        return $normalized === 'infinity' || $normalized === '-infinity';
    }

    /**
     * @param string $rangeString The PostgreSQL range string (e.g., '[1,10)', 'empty')
     */
    public static function fromString(string $rangeString): static
    {
        $rangeString = \trim($rangeString);

        if ($rangeString === self::EMPTY_RANGE_STRING) {
            // PostgreSQL's explicit empty state rather than mathematical tricks
            return new static(null, null, true, false, true);
        }

        $pattern = '/^('.\preg_quote(self::BRACKET_LOWER_INCLUSIVE, '/').'|'.\preg_quote(self::BRACKET_LOWER_EXCLUSIVE, '/').')("?[^",]*"?),("?[^",]*"?)('.\preg_quote(self::BRACKET_UPPER_INCLUSIVE, '/').'|'.\preg_quote(self::BRACKET_UPPER_EXCLUSIVE, '/').')$/';
        if (!\preg_match($pattern, $rangeString, $matches)) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid range format: %s', $rangeString)
            );
        }

        $isLowerBracketInclusive = $matches[1] === self::BRACKET_LOWER_INCLUSIVE;
        $isUpperBracketInclusive = $matches[4] === self::BRACKET_UPPER_INCLUSIVE;

        $lowerBoundString = \trim($matches[2], '"');
        $upperBoundString = \trim($matches[3], '"');

        $isLowerBoundedInfinity = false;
        $isUpperBoundedInfinity = false;
        $lowerBoundValue = null;
        $upperBoundValue = null;

        if ($matches[2] !== '') {
            $isLowerBoundedInfinity = static::isInfinityString($lowerBoundString);
            $lowerBoundValue = static::parseValue($lowerBoundString);
        }

        if ($matches[3] !== '') {
            $isUpperBoundedInfinity = static::isInfinityString($upperBoundString);
            $upperBoundValue = static::parseValue($upperBoundString);
        }

        return new static($lowerBoundValue, $upperBoundValue, $isLowerBracketInclusive, $isUpperBracketInclusive, false, $isLowerBoundedInfinity, $isUpperBoundedInfinity);
    }

    abstract protected static function parseValue(string $value): mixed;

    public function contains(mixed $target): bool
    {
        if ($target === null) {
            return false;
        }

        if ($this->isEmpty()) {
            return false;
        }

        // Check lower bound
        if ($this->lower !== null) {
            $comparison = $this->compareBounds($target, $this->lower);
            if ($comparison < 0 || ($comparison === 0 && !$this->isLowerBracketInclusive)) {
                return false;
            }
        }

        // Check upper bound
        if ($this->upper !== null) {
            $comparison = $this->compareBounds($target, $this->upper);
            if ($comparison > 0 || ($comparison === 0 && !$this->isUpperBracketInclusive)) {
                return false;
            }
        }

        return true;
    }

    public static function empty(): static
    {
        return new static(null, null, true, false, true);
    }

    public static function infinite(): static
    {
        return new static(null, null, false, false);
    }

    public function getLower(): \DateTimeInterface|float|int|null
    {
        return $this->lower;
    }

    public function getUpper(): \DateTimeInterface|float|int|null
    {
        return $this->upper;
    }

    public function isLowerBracketInclusive(): bool
    {
        return $this->isLowerBracketInclusive;
    }

    public function isUpperBracketInclusive(): bool
    {
        return $this->isUpperBracketInclusive;
    }

    public function isExplicitlyEmpty(): bool
    {
        return $this->isExplicitlyEmpty;
    }

    public function isLowerBoundedInfinity(): bool
    {
        return $this->isLowerBoundedInfinity;
    }

    public function isUpperBoundedInfinity(): bool
    {
        return $this->isUpperBoundedInfinity;
    }
}
