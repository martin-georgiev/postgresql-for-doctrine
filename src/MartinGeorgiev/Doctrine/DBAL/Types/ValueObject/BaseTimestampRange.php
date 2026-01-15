<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidRangeForPHPException;

/**
 * @extends Range<\DateTimeInterface>
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseTimestampRange extends Range
{
    public function __construct(
        ?\DateTimeInterface $lower,
        ?\DateTimeInterface $upper,
        bool $isLowerBracketInclusive = true,
        bool $isUpperBracketInclusive = false,
        bool $isExplicitlyEmpty = false,
        bool $isLowerBoundedInfinity = false,
        bool $isUpperBoundedInfinity = false,
    ) {
        parent::__construct($lower, $upper, $isLowerBracketInclusive, $isUpperBracketInclusive, $isExplicitlyEmpty, $isLowerBoundedInfinity, $isUpperBoundedInfinity);
    }

    protected function compareBounds(mixed $a, mixed $b): int
    {
        if (!$a instanceof \DateTimeInterface) {
            throw InvalidRangeForPHPException::forInvalidDateTimeBound($a);
        }

        if (!$b instanceof \DateTimeInterface) {
            throw InvalidRangeForPHPException::forInvalidDateTimeBound($b);
        }

        $timestampComparison = $a->getTimestamp() <=> $b->getTimestamp();
        if ($timestampComparison !== 0) {
            return $timestampComparison;
        }

        // PHP's getTimestamp() only returns seconds, so we need to separate the microsecond comparison.
        return (int) $a->format('u') <=> (int) $b->format('u');
    }

    protected static function parseValue(string $value): ?\DateTimeImmutable
    {
        if (static::isInfinityString($value)) {
            return null;
        }

        try {
            return new \DateTimeImmutable($value);
        } catch (\Exception $exception) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid timestamp value: %s. Error: %s', $value, $exception->getMessage()),
                0,
                $exception
            );
        }
    }
}
