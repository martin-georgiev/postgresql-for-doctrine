<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * Represents a PostgreSQL DATEMULTIRANGE value.
 *
 * @extends Multirange<DateRange>
 *
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class DateMultirange extends Multirange
{
    protected static function parseRange(string $rangeString): DateRange
    {
        return DateRange::fromString($rangeString);
    }
}
