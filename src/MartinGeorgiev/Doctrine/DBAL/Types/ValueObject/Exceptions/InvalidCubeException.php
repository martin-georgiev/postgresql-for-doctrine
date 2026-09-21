<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

/**
 * Exception thrown when creating or manipulating Cube value objects with invalid data.
 *
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class InvalidCubeException extends \InvalidArgumentException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidFormat(string $value): self
    {
        return self::create('Invalid cube format: %s', $value);
    }

    public static function forEmptyCoordinates(): self
    {
        return new self('A cube must have at least one dimension, none given');
    }

    public static function forTooManyDimensions(int $dimensions): self
    {
        return self::create('A cube cannot have more than 100 dimensions, %s given', $dimensions);
    }

    public static function forMismatchedDimensions(int $firstCornerDimensions, int $secondCornerDimensions): self
    {
        return new self(\sprintf(
            'Both cube corners must have the same number of dimensions, %d and %d given',
            $firstCornerDimensions,
            $secondCornerDimensions
        ));
    }
}
