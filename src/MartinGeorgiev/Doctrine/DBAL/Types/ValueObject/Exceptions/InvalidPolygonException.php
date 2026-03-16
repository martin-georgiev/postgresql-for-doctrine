<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

/**
 * Exception thrown when creating or manipulating Polygon value objects with invalid data.
 *
 * @since 4.4
 */
final class InvalidPolygonException extends \InvalidArgumentException
{
    public static function forInvalidFormat(string $value): self
    {
        return new self(\sprintf(
            'Invalid polygon format. Expected format "((x1,y1),(x2,y2),...)", got: %s',
            \var_export($value, true)
        ));
    }
}
