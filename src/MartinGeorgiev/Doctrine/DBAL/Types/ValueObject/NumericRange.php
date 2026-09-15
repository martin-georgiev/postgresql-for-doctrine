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
        if (!\is_numeric($bound)) {
            throw new \InvalidArgumentException(
                \sprintf('%s bound must be numeric, %s given', $position, \gettype($bound))
            );
        }

        if (!\is_finite((float) $bound)) {
            throw new \InvalidArgumentException(
                \sprintf('%s bound must be a finite number, %s given', $position, \var_export($bound, true))
            );
        }
    }

    protected function compareBounds(mixed $a, mixed $b): int
    {
        if (!\is_numeric($a)) {
            throw InvalidRangeForPHPException::forInvalidNumericBound($a);
        }

        if (!\is_numeric($b)) {
            throw InvalidRangeForPHPException::forInvalidNumericBound($b);
        }

        return (float) $a <=> (float) $b;
    }

    protected function formatValue(mixed $value): string
    {
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
