<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * Represents a PostgreSQL NUMMULTIRANGE value.
 *
 * @extends Multirange<NumericRange>
 *
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
