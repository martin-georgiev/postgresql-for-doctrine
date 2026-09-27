<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\Traits\PostgresInfinityConversionTrait;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidRangeException;

/**
 * @template R
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class Range implements \Stringable
{
    use PostgresInfinityConversionTrait;

    /**
     * @var string
     */
    protected const BRACKET_LOWER_INCLUSIVE = '[';

    /**
     * @var string
     */
    protected const BRACKET_LOWER_EXCLUSIVE = '(';

    /**
     * @var string
     */
    protected const BRACKET_UPPER_INCLUSIVE = ']';

    /**
     * @var string
     */
    protected const BRACKET_UPPER_EXCLUSIVE = ')';

    /**
     * @var string
     */
    protected const EMPTY_RANGE_STRING = 'empty';

    /**
     * @param R|null $lower
     * @param R|null $upper
     * @param bool $isLowerBoundedInfinity For types supporting infinity (timestamps, dates, numeric), indicates lower bound is explicitly infinity
     * @param bool $isUpperBoundedInfinity For types supporting infinity (timestamps, dates, numeric), indicates upper bound is explicitly infinity
     * @param bool $isLowerInfinityNegative PostgreSQL also takes `[infinity,)`, a lower bound above every finite value
     * @param bool $isUpperInfinityNegative PostgreSQL also takes `(,-infinity]`, an upper bound below every finite value
     */
    public function __construct(
        protected readonly mixed $lower,
        protected readonly mixed $upper,
        protected readonly bool $isLowerBracketInclusive = true,
        protected readonly bool $isUpperBracketInclusive = false,
        protected readonly bool $isExplicitlyEmpty = false,
        protected readonly bool $isLowerBoundedInfinity = false,
        protected readonly bool $isUpperBoundedInfinity = false,
        protected readonly bool $isLowerInfinityNegative = true,
        protected readonly bool $isUpperInfinityNegative = false,
    ) {}

    public function __toString(): string
    {
        if ($this->isEmpty()) {
            return self::EMPTY_RANGE_STRING;
        }

        $lowerBracket = $this->isLowerBracketInclusive ? self::BRACKET_LOWER_INCLUSIVE : self::BRACKET_LOWER_EXCLUSIVE;
        $upperBracket = $this->isUpperBracketInclusive ? self::BRACKET_UPPER_INCLUSIVE : self::BRACKET_UPPER_EXCLUSIVE;

        $formattedLowerBound = $this->isLowerBoundedInfinity ? static::formatInfinityBound($this->isLowerInfinityNegative) : ($this->lower === null ? '' : $this->formatValue($this->lower));
        $formattedUpperBound = $this->isUpperBoundedInfinity ? static::formatInfinityBound($this->isUpperInfinityNegative) : ($this->upper === null ? '' : $this->formatValue($this->upper));

        return $lowerBracket.$formattedLowerBound.','.$formattedUpperBound.$upperBracket;
    }

    protected static function formatInfinityBound(bool $isNegative): string
    {
        return $isNegative ? '-infinity' : 'infinity';
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

        $isLowerUnbounded = $this->lower === null && !$this->isLowerBoundedInfinity;
        $isUpperUnbounded = $this->upper === null && !$this->isUpperBoundedInfinity;
        if ($isLowerUnbounded || $isUpperUnbounded) {
            return false;
        }

        $comparison = $this->compareLowerWithUpper();
        if ($comparison > 0) {
            return true;
        }

        return $comparison === 0 && (!$this->isLowerBracketInclusive || !$this->isUpperBracketInclusive);
    }

    private function compareLowerWithUpper(): int
    {
        if ($this->isLowerBoundedInfinity && $this->isUpperBoundedInfinity) {
            $lowerSign = $this->isLowerInfinityNegative ? -1 : 1;
            $upperSign = $this->isUpperInfinityNegative ? -1 : 1;

            return $lowerSign <=> $upperSign;
        }

        if ($this->isLowerBoundedInfinity) {
            return -$this->compareWithInfinity($this->upper, $this->isLowerInfinityNegative);
        }

        if ($this->isUpperBoundedInfinity) {
            return $this->compareWithInfinity($this->lower, $this->isUpperInfinityNegative);
        }

        return $this->compareBounds($this->lower, $this->upper);
    }

    abstract protected function compareBounds(mixed $a, mixed $b): int;

    /**
     * Every value the subtype holds in PHP is finite, so it lies above negative infinity and below positive infinity.
     */
    protected function compareWithInfinity(mixed $value, bool $isNegative): int
    {
        return $isNegative ? 1 : -1;
    }

    abstract protected function formatValue(mixed $value): string;

    protected static function isInfinityString(string $value): bool
    {
        return self::normalizeInfinity($value) !== null;
    }

    protected static function isNegativeInfinityString(string $value): bool
    {
        return self::normalizeInfinity($value) === '-infinity';
    }

    /**
     * @param string $rangeString The PostgreSQL range string (e.g., '[1,10)', 'empty')
     */
    public static function fromString(string $rangeString): static
    {
        $rangeString = \trim($rangeString);

        if ($rangeString === self::EMPTY_RANGE_STRING) {
            return static::empty();
        }

        $pattern = '/^('.\preg_quote(self::BRACKET_LOWER_INCLUSIVE, '/').'|'.\preg_quote(self::BRACKET_LOWER_EXCLUSIVE, '/').')("?[^",]*"?),("?[^",]*"?)('.\preg_quote(self::BRACKET_UPPER_INCLUSIVE, '/').'|'.\preg_quote(self::BRACKET_UPPER_EXCLUSIVE, '/').')$/';
        if (!\preg_match($pattern, $rangeString, $matches)) {
            throw InvalidRangeException::forInvalidFormat($rangeString);
        }

        $isLowerBracketInclusive = $matches[1] === self::BRACKET_LOWER_INCLUSIVE;
        $isUpperBracketInclusive = $matches[4] === self::BRACKET_UPPER_INCLUSIVE;

        $lowerBoundString = \trim($matches[2], '"');
        $upperBoundString = \trim($matches[3], '"');

        $isLowerBoundedInfinity = false;
        $isUpperBoundedInfinity = false;
        $isLowerInfinityNegative = true;
        $isUpperInfinityNegative = false;
        /** @var R|null $lowerBoundValue */
        $lowerBoundValue = null;
        /** @var R|null $upperBoundValue */
        $upperBoundValue = null;

        if ($matches[2] !== '') {
            $isLowerBoundedInfinity = static::isInfinityString($lowerBoundString);
            $isLowerInfinityNegative = !$isLowerBoundedInfinity || static::isNegativeInfinityString($lowerBoundString);
            /** @var R|null $lowerBoundValue */
            $lowerBoundValue = static::parseValue($lowerBoundString);
        }

        if ($matches[3] !== '') {
            $isUpperBoundedInfinity = static::isInfinityString($upperBoundString);
            $isUpperInfinityNegative = $isUpperBoundedInfinity && static::isNegativeInfinityString($upperBoundString);
            /** @var R|null $upperBoundValue */
            $upperBoundValue = static::parseValue($upperBoundString);
        }

        return new static($lowerBoundValue, $upperBoundValue, $isLowerBracketInclusive, $isUpperBracketInclusive, false, $isLowerBoundedInfinity, $isUpperBoundedInfinity, $isLowerInfinityNegative, $isUpperInfinityNegative); // @phpstan-ignore new.static
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
        $comparison = match (true) {
            $this->isLowerBoundedInfinity => $this->compareWithInfinity($target, $this->isLowerInfinityNegative),
            $this->lower !== null => $this->compareBounds($target, $this->lower),
            default => null,
        };
        if ($comparison !== null && ($comparison < 0 || ($comparison === 0 && !$this->isLowerBracketInclusive))) {
            return false;
        }

        // Check upper bound
        $comparison = match (true) {
            $this->isUpperBoundedInfinity => $this->compareWithInfinity($target, $this->isUpperInfinityNegative),
            $this->upper !== null => $this->compareBounds($target, $this->upper),
            default => null,
        };
        if ($comparison !== null && ($comparison > 0 || ($comparison === 0 && !$this->isUpperBracketInclusive))) {
            return false;
        }

        return true;
    }

    public static function empty(): static
    {
        return new static(null, null, true, false, true); // @phpstan-ignore new.static, return.type
    }

    public static function infinite(): static
    {
        return new static(null, null, false, false); // @phpstan-ignore new.static, return.type
    }

    /**
     * @return R|null
     */
    public function getLower(): mixed
    {
        return $this->lower;
    }

    /**
     * @return R|null
     */
    public function getUpper(): mixed
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
