<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidRangeForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\PostgresFloatConversionTrait;

/**
 * Represents a PostgreSQL numeric range.
 *
 * @extends Range<float|int>
 *
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
    ) {
        $normalizedLower = $lower;
        $normalizedUpper = $upper;
        $inferredLowerBoundedInfinityFlag = $isLowerBoundedInfinity;
        $inferredUpperBoundedInfinityFlag = $isUpperBoundedInfinity;

        if ($lower !== null && \is_float($lower) && \is_infinite($lower)) {
            $normalizedLower = null;
            $inferredLowerBoundedInfinityFlag = true;
        } elseif ($lower !== null) {
            $this->assertUsableBound($lower, 'Lower');
        }

        if ($upper !== null && \is_float($upper) && \is_infinite($upper)) {
            $normalizedUpper = null;
            $inferredUpperBoundedInfinityFlag = true;
        } elseif ($upper !== null) {
            $this->assertUsableBound($upper, 'Upper');
        }

        parent::__construct($normalizedLower, $normalizedUpper, $isLowerBracketInclusive, $isUpperBracketInclusive, $isExplicitlyEmpty, $inferredLowerBoundedInfinityFlag, $inferredUpperBoundedInfinityFlag);
    }

    private function assertUsableBound(mixed $bound, string $position): void
    {
        if ($this->isNotANumber($bound)) {
            return;
        }

        if (!\is_numeric($bound)) {
            throw new \InvalidArgumentException(
                \sprintf('%s bound must be numeric, %s given', $position, \gettype($bound))
            );
        }

        if (!\is_finite((float) $bound)) {
            throw new \InvalidArgumentException(
                \sprintf('%s bound must be a number a PHP float can hold, %s given', $position, \var_export($bound, true))
            );
        }
    }

    /**
     * @phpstan-assert-if-true float $value
     */
    private function isNotANumber(mixed $value): bool
    {
        return \is_float($value) && \is_nan($value);
    }

    /**
     * The spelling `numeric` reads for NaN. It is narrower than the float8 one the trait describes, which also takes a
     * sign: `SELECT '-nan'::numeric` is an error while `SELECT '-nan'::float8` is not.
     */
    private static function isNotANumberString(string $value): bool
    {
        return \mb_strtolower($value) === 'nan';
    }

    /**
     * PostgreSQL gives `numeric` a total order that puts NaN above every other value, itself included, so a range can
     * be bounded by it. PHP's spaceship operator instead answers 1 for every comparison involving NAN.
     */
    protected function compareBounds(mixed $a, mixed $b): int
    {
        if (!\is_numeric($a)) {
            throw InvalidRangeForPHPException::forInvalidNumericBound($a);
        }

        if (!\is_numeric($b)) {
            throw InvalidRangeForPHPException::forInvalidNumericBound($b);
        }

        $aIsNotANumber = $this->isNotANumber($a);
        $bIsNotANumber = $this->isNotANumber($b);
        if ($aIsNotANumber || $bIsNotANumber) {
            return $aIsNotANumber <=> $bIsNotANumber;
        }

        return (float) $a <=> (float) $b;
    }

    /**
     * An infinity bound is kept as a flag rather than as a value, so the base class sees the open end that a missing
     * bound also produces. NaN is the one value that tells them apart: it sorts above `Infinity`, which makes
     * `[1,Infinity)` exclude it while the unbounded `[1,)` contains it.
     */
    public function contains(mixed $target): bool
    {
        if ($this->isNotANumber($target) && $this->isUpperBoundedInfinity()) {
            return false;
        }

        return parent::contains($target);
    }

    protected function formatValue(mixed $value): string
    {
        if ($this->isNotANumber($value)) {
            return self::formatFloat($value);
        }

        if (!\is_numeric($value)) {
            throw new \InvalidArgumentException('Value must be numeric');
        }

        return (string) $value;
    }

    protected static function isInfinityString(string $value): bool
    {
        return self::isNonFiniteString($value) && \is_infinite(self::parseFloat($value));
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
            throw new \InvalidArgumentException(
                \sprintf('Invalid numeric value: %s', $value)
            );
        }

        $floatValue = (float) $value;
        if (!\is_finite($floatValue)) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid numeric value: %s', $value)
            );
        }

        $intValue = (int) $floatValue;

        return $floatValue === (float) $intValue ? $intValue : $floatValue;
    }
}
