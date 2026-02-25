<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * Represents a PostgreSQL INT4MULTIRANGE value.
 *
 * @extends Multirange<Int4Range>
 *
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Int4Multirange extends Multirange
{
    protected static function parseRange(string $rangeString): Int4Range
    {
        return Int4Range::fromString($rangeString);
    }
}
