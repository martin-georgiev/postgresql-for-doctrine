<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * Abstract base class for PostgreSQL range types.
 *
 * @template T of int|float|\DateTimeInterface
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

    public function __construct(
        protected readonly mixed $lower,
        protected readonly mixed $upper,
        protected readonly bool $isLowerBracketInclusive = true,
        protected readonly bool $isUpperBracketInclusive = false,
        protected readonly bool $isExplicitlyEmpty = false,
    ) {}

    public function __toString(): string
    {
        if ($this->isEmpty()) {
            return self::EMPTY_RANGE_STRING;
        }

        $lowerBracket = $this->isLowerBracketInclusive ? self::BRACKET_LOWER_INCLUSIVE : self::BRACKET_LOWER_EXCLUSIVE;
        $upperBracket = $this->isUpperBracketInclusive ? self::BRACKET_UPPER_INCLUSIVE : self::BRACKET_UPPER_EXCLUSIVE;

        $formattedLowerBound = $this->lower === null ? '' : $this->formatValue($this->lower);
        $formattedUpperBound = $this->upper === null ? '' : $this->formatValue($this->upper);

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
        $lowerBoundValue = $matches[2] === '' ? null : static::parseValue(\trim($matches[2], '"'));
        $upperBoundValue = $matches[3] === '' ? null : static::parseValue(\trim($matches[3], '"'));

        return new static($lowerBoundValue, $upperBoundValue, $isLowerBracketInclusive, $isUpperBracketInclusive);
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

    /**
     * Uses PostgreSQL's explicit empty state rather than mathematical tricks.
     */
    public static function empty(): static
    {
        return new static(null, null, true, false, true);
    }

    public static function infinite(): static
    {
        return new static(null, null, true, true);
    }
}
