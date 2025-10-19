<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidRangeForPHPException;

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
    public function __construct(
        mixed $lower,
        mixed $upper,
        bool $isLowerBracketInclusive = true,
        bool $isUpperBracketInclusive = false,
        bool $isExplicitlyEmpty = false,
    ) {
        if ($lower !== null && !\is_numeric($lower)) {
            throw new \InvalidArgumentException(
                \sprintf('Lower bound must be numeric, %s given', \gettype($lower))
            );
        }

        if ($upper !== null && !\is_numeric($upper)) {
            throw new \InvalidArgumentException(
                \sprintf('Upper bound must be numeric, %s given', \gettype($upper))
            );
        }

        parent::__construct($lower, $upper, $isLowerBracketInclusive, $isUpperBracketInclusive, $isExplicitlyEmpty);
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

    protected static function parseValue(string $value): float|int
    {
        if (!\is_numeric($value)) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid numeric value: %s', $value)
            );
        }

        $floatValue = (float) $value;
        $intValue = (int) $floatValue;

        return $floatValue === (float) $intValue ? $intValue : $floatValue;
    }
}
