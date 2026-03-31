<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

/**
 * Exception thrown when creating or manipulating Circle value objects with invalid data.
 *
 * @since 4.5
 */
final class InvalidCircleException extends \InvalidArgumentException
{
    public static function forInvalidFormat(string $value, string $expectedPattern, ?\Throwable $throwable = null): self
    {
        return new self(\sprintf(
            'Invalid circle format. Expected format matching %s, got: %s',
            \var_export($expectedPattern, true),
            \var_export($value, true)
        ), 0, $throwable);
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
