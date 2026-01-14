<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * Represents a PostgreSQL INT4RANGE (32-bit integer range).
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Int4Range extends BaseIntegerRange
{
    private const MIN_INT4_VALUE = -2147483648;

    private const MAX_INT4_VALUE = 2147483647;

    public function __construct(
        ?int $lower,
        ?int $upper,
        bool $isLowerBracketInclusive = true,
        bool $isUpperBracketInclusive = false,
        bool $isExplicitlyEmpty = false,
        bool $isLowerBoundedInfinity = false,
        bool $isUpperBoundedInfinity = false,
    ) {
        if ($lower !== null && ($lower < self::MIN_INT4_VALUE || $lower > self::MAX_INT4_VALUE)) {
            throw new \InvalidArgumentException(
                \sprintf('Lower bound %d is outside INT4 range [%d, %d]', $lower, self::MIN_INT4_VALUE, self::MAX_INT4_VALUE)
            );
        }

        if ($upper !== null && ($upper < self::MIN_INT4_VALUE || $upper > self::MAX_INT4_VALUE)) {
            throw new \InvalidArgumentException(
                \sprintf('Upper bound %d is outside INT4 range [%d, %d]', $upper, self::MIN_INT4_VALUE, self::MAX_INT4_VALUE)
            );
        }

        parent::__construct($lower, $upper, $isLowerBracketInclusive, $isUpperBracketInclusive, $isExplicitlyEmpty, $isLowerBoundedInfinity, $isUpperBoundedInfinity);
    }
}
