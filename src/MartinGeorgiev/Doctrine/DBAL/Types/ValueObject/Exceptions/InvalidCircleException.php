<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

/**
 * Exception thrown when creating or manipulating Circle value objects with invalid data.
 *
 * @since 4.4
 */
final class InvalidCircleException extends \InvalidArgumentException
{
    public static function forInvalidFormat(string $value): self
    {
        return new self(\sprintf(
            'Invalid circle format. Expected format "<(x,y),r>", got: %s',
            \var_export($value, true)
        ));
    }
}
