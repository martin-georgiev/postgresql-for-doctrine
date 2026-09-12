<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\Traits\PostgresInfinityConversionTrait;

/**
 * Represents PostgreSQL infinity values for a date, timestamp and timestamptz fields.
 *
 * An infinity value shall sort before and after every other value of their type:
 *  - negative infinity is meant to represent a date earlier than any other.
 *  - positive infinity is meant to represent a date later than any other.
 *
 * @see https://www.postgresql.org/docs/18/datatype-datetime.html#DATATYPE-DATETIME-SPECIAL-TABLE
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
enum DateTimeInfinity: string
{
    use PostgresInfinityConversionTrait;

    /**
     * PHP forbids redeclaring an enum's own `tryFrom()`, so the tolerant lookup needs a name of its
     * own. It follows the `fromString()` the value objects use. The backing values are the spellings
     * PostgreSQL emits, while it reads any case and an explicit `+` besides.
     */
    public static function tryFromString(string $value): ?self
    {
        $canonical = self::normalizeInfinity($value);

        return $canonical === null ? null : self::from($canonical);
    }

    case POSITIVE = 'infinity';

    case NEGATIVE = '-infinity';
}
