<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

/**
 * Exception thrown when creating or manipulating Line value objects with invalid data.
 *
 * @since 4.5
 */
final class InvalidLineException extends \InvalidArgumentException
{
    public static function forInvalidFormat(string $value): self
    {
        return new self(\sprintf(
            'Invalid line format. Expected format "{A,B,C}", got: %s',
            \var_export($value, true)
        ));
    }
}
