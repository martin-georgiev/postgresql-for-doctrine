<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * Base class for PostgreSQL integer range types.
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseIntegerRange extends Range
{
    public function __construct(
        ?int $lower,
        ?int $upper,
        bool $isLowerBracketInclusive = true,
        bool $isUpperBracketInclusive = false,
        bool $isExplicitlyEmpty = false,
    ) {
        parent::__construct($lower, $upper, $isLowerBracketInclusive, $isUpperBracketInclusive, $isExplicitlyEmpty);
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
        $lowerBoundValue = $matches[2] === '' ? null : static::parseValue(\trim($matches[2], '"'));
        $upperBoundValue = $matches[3] === '' ? null : static::parseValue(\trim($matches[3], '"'));

        return new static($lowerBoundValue, $upperBoundValue, $isLowerBracketInclusive, $isUpperBracketInclusive);
    }

    protected function compareBounds(mixed $a, mixed $b): int
    {
        return $a <=> $b;
    }

    protected function formatValue(mixed $value): string
    {
        return (string) $value;
    }

    protected static function parseValue(string $value): int
    {
        if (!\is_numeric($value)) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid integer value: %s', $value)
            );
        }

        $intValue = (int) $value;
        if ((string) $intValue !== $value) {
            throw new \InvalidArgumentException(
                \sprintf('Value %s is not a valid integer', $value)
            );
        }

        return $intValue;
    }
}
