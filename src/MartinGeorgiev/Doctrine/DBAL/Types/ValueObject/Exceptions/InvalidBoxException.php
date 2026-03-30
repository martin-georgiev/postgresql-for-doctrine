<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

/**
 * Exception thrown when creating or manipulating Box value objects with invalid data.
 *
 * @since 4.5
 */
final class InvalidBoxException extends \InvalidArgumentException
{
    public static function forInvalidFormat(string $value): self
    {
        return new self(\sprintf(
            'Invalid box format. Expected format "(x1,y1),(x2,y2)", got: %s',
            \var_export($value, true)
        ));
    }
}
