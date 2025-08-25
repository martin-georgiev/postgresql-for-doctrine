<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

/**
 * Exception thrown when creating or manipulating Ltree value objects with invalid data.
 *
 * This exception is specifically for validation errors within the Ltree value object itself,
 * separate from DBAL conversion exceptions.
 *
 * @since 3.5
 */
final class InvalidLtreeException extends \InvalidArgumentException
{
    /**
     * @param mixed[] $value
     */
    public static function forInvalidPathFromRootFormat(array $value, string $expectedFormat): self
    {
        return new self(\sprintf(
            "Invalid Ltree's path from root format. Expected %s, got: %s",
            $expectedFormat,
            \var_export($value, true)
        ));
    }

    public static function forInvalidNodeFormat(mixed $value, string $expectedFormat): self
    {
        return new self(\sprintf(
            'Invalid node format: Expected %s, got: %s',
            $expectedFormat,
            \var_export($value, true)
        ));
    }
}
