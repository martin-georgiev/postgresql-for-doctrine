<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

/**
 * Exception thrown when creating or manipulating Point value objects with invalid data.
 *
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class InvalidPointException extends \InvalidArgumentException
{
    public static function forInvalidFormat(string $pointString, string $expectedPattern): self
    {
        return new self(\sprintf(
            'Invalid point format. Expected format matching %s, got: %s',
            \var_export($expectedPattern, true),
            \var_export($pointString, true)
        ));
    }
}
