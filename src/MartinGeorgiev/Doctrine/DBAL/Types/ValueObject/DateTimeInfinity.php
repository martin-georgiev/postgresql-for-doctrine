<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

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
    case POSITIVE = 'infinity';

    case NEGATIVE = '-infinity';
}
