<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * Represents a PostgreSQL NUMMULTIRANGE value.
 *
 * @extends Multirange<NumericRange>
 *
 * @see https://www.postgresql.org/docs/18/rangetypes.html
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class NumericMultirange extends Multirange
{
    protected static function parseRange(string $rangeString): NumericRange
    {
        return NumericRange::fromString($rangeString);
    }
}
