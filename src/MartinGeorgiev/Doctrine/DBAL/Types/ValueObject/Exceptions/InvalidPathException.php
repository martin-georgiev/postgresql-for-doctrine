<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

/**
 * Exception thrown when creating or manipulating Path value objects with invalid data.
 *
 * @since 4.4
 */
final class InvalidPathException extends \InvalidArgumentException
{
    public static function forInvalidFormat(string $value): self
    {
        return new self(\sprintf(
            'Invalid path format. Expected format "[(x1,y1),...]" (open) or "((x1,y1),...)" (closed), got: %s',
            \var_export($value, true)
        ));
    }
}
