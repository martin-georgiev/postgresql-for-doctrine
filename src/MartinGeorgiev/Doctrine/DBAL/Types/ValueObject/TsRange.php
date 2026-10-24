<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidRangeException;

/**
 * Represents a PostgreSQL timestamp range without timezone.
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class TsRange extends BaseTimestampRange
{
    /**
     * A tsrange holds wall-clock times, and formatValue() writes each bound's own wall clock whatever its time zone,
     * as Doctrine's datetime types do. Comparing the instants instead would save a valid range as empty, or write
     * bounds PostgreSQL rejects.
     */
    protected function compareBounds(mixed $a, mixed $b): int
    {
        if (!$a instanceof \DateTimeInterface) {
            throw InvalidRangeException::forInvalidBoundType(\DateTimeInterface::class, $a);
        }

        if (!$b instanceof \DateTimeInterface) {
            throw InvalidRangeException::forInvalidBoundType(\DateTimeInterface::class, $b);
        }

        return $this->wallClock($a) <=> $this->wallClock($b);
    }

    /**
     * A value from another time zone is moved onto the bounds' clock first, so it is still compared by its instant.
     */
    public function contains(mixed $target): bool
    {
        $bound = $this->lower ?? $this->upper;
        if ($target instanceof \DateTimeInterface && $bound instanceof \DateTimeInterface) {
            $target = \DateTimeImmutable::createFromInterface($target)->setTimezone($bound->getTimezone());
        }

        return parent::contains($target);
    }

    /**
     * @return array{int, int, int, int, int, int, int}
     */
    private function wallClock(\DateTimeInterface $value): array
    {
        return [
            (int) $value->format('Y'),
            (int) $value->format('n'),
            (int) $value->format('j'),
            (int) $value->format('G'),
            (int) $value->format('i'),
            (int) $value->format('s'),
            (int) $value->format('u'),
        ];
    }

    protected function formatValue(mixed $value): string
    {
        if (!$value instanceof \DateTimeInterface) {
            throw InvalidRangeException::forInvalidBoundType(\DateTimeInterface::class, $value);
        }

        return self::formatInPostgresEra($value, 'Y-m-d H:i:s.u');
    }
}
