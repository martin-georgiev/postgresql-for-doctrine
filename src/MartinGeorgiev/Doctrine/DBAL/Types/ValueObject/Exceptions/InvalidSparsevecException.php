<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

/**
 * Exception thrown when creating Sparsevec value objects with invalid data.
 *
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class InvalidSparsevecException extends \InvalidArgumentException
{
    public static function forNonPositiveDimensions(int $dimensions): self
    {
        return new self(\sprintf(
            'Dimensions must be a positive integer, got %d',
            $dimensions
        ));
    }

    public static function forElementKeyOutOfRange(int $key, int $dimensions): self
    {
        return new self(\sprintf(
            'Element key must be between 1 and %d, got %d',
            $dimensions,
            $key
        ));
    }

    public static function forInvalidElementValue(int $key, mixed $value): self
    {
        return new self(\sprintf(
            'Element value at key %d must be int or float, got %s',
            $key,
            \var_export($value, true)
        ));
    }
}
