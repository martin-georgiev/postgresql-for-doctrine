<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidRangeException;

/**
 * Represents a PostgreSQL INT4RANGE (32-bit integer range).
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Int4Range extends BaseIntegerRange
{
    /**
     * @var int
     */
    private const MIN_INT4_VALUE = -2147483648;

    /**
     * @var int
     */
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
            throw InvalidRangeException::forBoundOutsideSubtypeRange($lower, self::MIN_INT4_VALUE, self::MAX_INT4_VALUE, InvalidRangeException::LOWER_BOUND);
        }

        if ($upper !== null && ($upper < self::MIN_INT4_VALUE || $upper > self::MAX_INT4_VALUE)) {
            throw InvalidRangeException::forBoundOutsideSubtypeRange($upper, self::MIN_INT4_VALUE, self::MAX_INT4_VALUE, InvalidRangeException::UPPER_BOUND);
        }

        parent::__construct($lower, $upper, $isLowerBracketInclusive, $isUpperBracketInclusive, $isExplicitlyEmpty, $isLowerBoundedInfinity, $isUpperBoundedInfinity);
    }
}
