<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * Represents a PostgreSQL INT8MULTIRANGE value.
 *
 * @extends Multirange<Int8Range>
 *
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Int8Multirange extends Multirange
{
    protected static function parseRange(string $rangeString): Int8Range
    {
        return Int8Range::fromString($rangeString);
    }
}
