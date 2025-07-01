<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * Base class for PostgreSQL timestamp range types.
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
    ) {
        parent::__construct($lower, $upper, $isLowerBracketInclusive, $isUpperBracketInclusive, $isExplicitlyEmpty);
    }

    protected function compareBounds(mixed $a, mixed $b): int
    {
        $timestampComparison = $a->getTimestamp() <=> $b->getTimestamp();
        if ($timestampComparison !== 0) {
            return $timestampComparison;
        }

        // PHP's getTimestamp() only returns seconds, so we need separate microsecond comparison.
        return (int) $a->format('u') <=> (int) $b->format('u');
    }

    protected function formatValue(mixed $value): string
    {
        if (!$value instanceof \DateTimeInterface) {
            throw new \InvalidArgumentException('Value must be a DateTimeInterface');
        }

        return $value->format('Y-m-d H:i:s.u');
    }

    protected static function parseValue(string $value): \DateTimeImmutable
    {
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
