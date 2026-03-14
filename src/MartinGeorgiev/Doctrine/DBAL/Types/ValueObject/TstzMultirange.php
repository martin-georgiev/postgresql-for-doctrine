<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * Represents a PostgreSQL TSTZMULTIRANGE value.
 *
 * @extends Multirange<TstzRange>
 *
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class TstzMultirange extends Multirange
{
    protected static function parseRange(string $rangeString): TstzRange
    {
        return TstzRange::fromString($rangeString);
    }
}
