<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

/**
 * Exception thrown when creating or manipulating Box value objects with invalid data.
 *
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class InvalidBoxException extends \InvalidArgumentException
{
    public static function forInvalidFormat(string $value, string $expectedPattern): self
    {
        return new self(\sprintf(
            'Invalid box format. Expected format matching %s, got: %s',
            \var_export($expectedPattern, true),
            \var_export($value, true)
        ));
    }
}
