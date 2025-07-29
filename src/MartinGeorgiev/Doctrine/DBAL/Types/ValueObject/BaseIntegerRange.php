<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidRangeForPHPException;

/**
 * @extends Range<int>
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

    protected function compareBounds(mixed $a, mixed $b): int
    {
        return $a <=> $b;
    }

    protected function formatValue(mixed $value): string
    {
        if (!\is_int($value)) {
            throw InvalidRangeForPHPException::forInvalidIntegerBound($value);
        }

        return (string) $value;
    }

    protected static function parseValue(string $value): int
    {
        $intValue = (int) $value;
        if ((string) $intValue !== $value) {
            throw new \InvalidArgumentException(
                \sprintf('Value %s is not a valid integer', $value)
            );
        }

        return $intValue;
    }
}
