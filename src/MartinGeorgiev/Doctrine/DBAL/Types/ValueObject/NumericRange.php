<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\Traits\PostgresFloatConversionTrait;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidRangeException;

/**
 * Represents a PostgreSQL numeric range.
 *
 * @extends Range<float|int>
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class NumericRange extends Range
{
    use PostgresFloatConversionTrait;

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
        $normalizedLower = $lower;
        $normalizedUpper = $upper;
        $inferredLowerBoundedInfinityFlag = $isLowerBoundedInfinity;
        $inferredUpperBoundedInfinityFlag = $isUpperBoundedInfinity;

        if ($lower !== null && \is_float($lower) && \is_infinite($lower)) {
            $normalizedLower = null;
            $inferredLowerBoundedInfinityFlag = true;
            $isLowerInfinityNegative = $lower < 0;
        } elseif ($lower !== null) {
            $this->assertUsableBound($lower, InvalidRangeException::LOWER_BOUND);
        }

        if ($upper !== null && \is_float($upper) && \is_infinite($upper)) {
            $normalizedUpper = null;
            $inferredUpperBoundedInfinityFlag = true;
            $isUpperInfinityNegative = $upper < 0;
        } elseif ($upper !== null) {
            $this->assertUsableBound($upper, InvalidRangeException::UPPER_BOUND);
        }

        parent::__construct($normalizedLower, $normalizedUpper, $isLowerBracketInclusive, $isUpperBracketInclusive, $isExplicitlyEmpty, $inferredLowerBoundedInfinityFlag, $inferredUpperBoundedInfinityFlag, $isLowerInfinityNegative, $isUpperInfinityNegative);
    }

    private function assertUsableBound(mixed $bound, string $position): void
    {
        if ($this->isNotANumber($bound)) {
            return;
        }

        if (!\is_numeric($bound)) {
            throw InvalidRangeException::forInvalidBoundType('numeric', $bound, $position);
        }

        if (!\is_finite((float) $bound)) {
            throw InvalidRangeException::forNonFiniteBound($bound, $position);
        }
    }

    /**
     * @phpstan-assert-if-true float $value
     */
    private function isNotANumber(mixed $value): bool
    {
        return \is_float($value) && \is_nan($value);
    }

    private static function isNotANumberString(string $value): bool
    {
        return \mb_strtolower($value) === 'nan';
    }

    /**
     * PostgreSQL gives `numeric` a total order that puts NaN above every other value, itself included.
     * A range can be bounded by it. PHP's spaceship operator instead answers 1 for every comparison involving NAN.
     */
    protected function compareBounds(mixed $a, mixed $b): int
    {
        if (!\is_numeric($a)) {
            throw InvalidRangeException::forInvalidBoundType('numeric', $a);
        }

        if (!\is_numeric($b)) {
            throw InvalidRangeException::forInvalidBoundType('numeric', $b);
        }

        $aIsNotANumber = $this->isNotANumber($a);
        $bIsNotANumber = $this->isNotANumber($b);
        if ($aIsNotANumber || $bIsNotANumber) {
            return $aIsNotANumber <=> $bIsNotANumber;
        }

        return (float) $a <=> (float) $b;
    }

    /**
     * NaN sorts above `Infinity`, which makes `[1,Infinity)` exclude it while the unbounded `[1,)` contains it.
     * Unlike the other subtypes, `numeric` also holds the infinities themselves.
     *
     * A numeric string is always finite, since is_numeric() accepts no spelling of infinity, but one beyond the float
     * range, such as `1e999`, casts to INF.
     */
    protected function compareWithInfinity(mixed $value, bool $isNegative): int
    {
        if (!\is_numeric($value)) {
            throw InvalidRangeException::forInvalidBoundType('numeric', $value);
        }

        if ($this->isNotANumber($value)) {
            return 1;
        }

        if (\is_string($value)) {
            return parent::compareWithInfinity($value, $isNegative);
        }

        return (float) $value <=> ($isNegative ? -\INF : \INF);
    }

    protected function formatValue(mixed $value): string
    {
        if (\is_float($value)) {
            return self::formatFloat($value);
        }

        if (!\is_numeric($value)) {
            throw InvalidRangeException::forInvalidBoundType('numeric', $value);
        }

        return (string) $value;
    }

    protected static function isInfinityString(string $value): bool
    {
        return self::isNonFiniteString($value) && \is_infinite(self::parseFloat($value));
    }

    protected static function isNegativeInfinityString(string $value): bool
    {
        return self::isInfinityString($value) && self::parseFloat($value) < 0;
    }

    protected static function formatInfinityBound(bool $isNegative): string
    {
        return self::formatFloat($isNegative ? -\INF : \INF);
    }

    protected static function parseValue(string $value): float|int|null
    {
        if (self::isInfinityString($value)) {
            return null;
        }

        if (self::isNotANumberString($value)) {
            return \NAN;
        }

        if (!\is_numeric($value)) {
            throw InvalidRangeException::forInvalidBoundType('numeric', $value);
        }

        // A float holds integers exactly only up to 2^53, so an integer literal is read as one directly.
        $intValue = \filter_var($value, \FILTER_VALIDATE_INT);
        if ($intValue !== false) {
            return $intValue;
        }

        $floatValue = (float) $value;
        if (!\is_finite($floatValue)) {
            throw InvalidRangeException::forNonFiniteBound($value);
        }

        $isWithinIntegerRange = $floatValue >= \PHP_INT_MIN && $floatValue < \PHP_INT_MAX;
        if ($isWithinIntegerRange && $floatValue === \floor($floatValue)) {
            return (int) $floatValue;
        }

        return $floatValue;
    }
}
