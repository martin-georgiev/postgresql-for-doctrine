<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

/**
 * Exception thrown when an invalid range value is provided for database conversion.
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class InvalidRangeForDatabaseException extends \InvalidArgumentException
{
    public static function forInvalidType(mixed $value): self
    {
        return new self(
            \sprintf(
                'Invalid type for range. Expected Range object or string, got %s',
                \get_debug_type($value)
            )
        );
    }

    public static function forInvalidFormat(string $value): self
    {
        return new self(
            \sprintf('Invalid range format: %s', $value)
        );
    }
}
