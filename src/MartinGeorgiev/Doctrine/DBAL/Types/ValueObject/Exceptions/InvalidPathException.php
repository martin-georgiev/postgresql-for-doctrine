<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

/**
 * Exception thrown when creating or manipulating Path value objects with invalid data.
 *
 * @since 4.5
 */
final class InvalidPathException extends \InvalidArgumentException
{
    public static function forInvalidFormat(string $value, string $expectedPattern): self
    {
        return new self(\sprintf(
            'Invalid path format. Expected format matching %s, got: %s',
            \var_export($expectedPattern, true),
            \var_export($value, true)
        ));
    }
}
