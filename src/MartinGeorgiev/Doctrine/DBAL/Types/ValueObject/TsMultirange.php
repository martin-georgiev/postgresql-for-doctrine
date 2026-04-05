<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * Represents a PostgreSQL TSMULTIRANGE value.
 *
 * @extends Multirange<TsRange>
 *
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class TsMultirange extends Multirange
{
    protected static function parseRange(string $rangeString): TsRange
    {
        return TsRange::fromString($rangeString);
    }
}
