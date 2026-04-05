<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

/**
 * Exception thrown when creating or manipulating Polygon value objects with invalid data.
 *
 * @since 4.5
 */
final class InvalidPolygonException extends \InvalidArgumentException
{
    public static function forInvalidFormat(string $value, string $expectedPattern): self
    {
        return new self(\sprintf(
            'Invalid polygon format. Expected format matching %s, got: %s',
            \var_export($expectedPattern, true),
            \var_export($value, true)
        ));
    }

    public static function forTooFewVertices(int $count): self
    {
        return new self(\sprintf(
            'A polygon requires at least 2 vertices, got %d',
            $count
        ));
    }
}
