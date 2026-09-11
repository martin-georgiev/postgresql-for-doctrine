<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * Represents the two values a PostgreSQL date, timestamp or timestamptz can hold that lie outside any calendar.
 *
 * They sort before and after every other value of their type, which is why PostgreSQL offers them instead of
 * a NULL: `-infinity` is not an unknown date, it is a date earlier than any other. \DateTimeImmutable has no
 * representation for either, so datetime array items carry this enum in their place rather than a date.
 *
 * The backing values are the literals PostgreSQL reads and writes.
 *
 * @see https://www.postgresql.org/docs/18/datatype-datetime.html#DATATYPE-DATETIME-SPECIAL-TABLE
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
enum DateTimeInfinity: string
{
    case POSITIVE = 'infinity';

    case NEGATIVE = '-infinity';
}
