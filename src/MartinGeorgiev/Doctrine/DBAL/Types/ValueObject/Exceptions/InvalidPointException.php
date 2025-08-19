<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

/**
 * Exception thrown when creating or manipulating Point value objects with invalid data.
 * 
 * This exception is specifically for validation errors within the Point value object itself,
 * separate from DBAL conversion exceptions.
 *
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class InvalidPointException extends \InvalidArgumentException
{
    public static function forInvalidPointFormat(string $pointString, string $expectedPattern): self
    {
        return new self(\sprintf(
            'Invalid point format. Expected format matching %s, got: %s',
            \var_export($expectedPattern, true),
            \var_export($pointString, true)
        ));
    }

    public static function forInvalidCoordinate(string $coordinateName, string $value): self
    {
        return new self(\sprintf(
            'Invalid %s coordinate format: %s',
            \var_export($coordinateName, true),
            \var_export($value, true)
        ));
    }
}
