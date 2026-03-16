<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * Value object representing a PostgreSQL interval value.
 *
 * Accepts any valid PostgreSQL interval string format:
 * - ISO 8601: P1Y2M3DT4H5M6S
 * - Verbose: 1 year 2 months 3 days 4 hours 5 minutes 6 seconds
 * - PostgreSQL: 1-2 3 4:05:06
 *
 * @see https://www.postgresql.org/docs/current/datatype-datetime.html#DATATYPE-INTERVAL-INPUT
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @phpstan-consistent-constructor
 */
class Interval implements \Stringable
{
    public function __construct(
        private readonly string $value,
    ) {}

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @throws \InvalidArgumentException if $value is an empty string
     */
    public static function fromString(string $value): static
    {
        if ('' === $value) {
            throw new \InvalidArgumentException('Interval value must be a non-empty string');
        }

        return new static($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
