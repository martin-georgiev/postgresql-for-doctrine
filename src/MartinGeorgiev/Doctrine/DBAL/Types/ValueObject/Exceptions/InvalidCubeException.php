<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class InvalidCubeException extends ConversionException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidFormat(mixed $value): self
    {
        return self::create('Invalid cube format: %s', $value);
    }

    public static function forEmptyCoordinates(mixed $value): self
    {
        return self::create('A cube must have at least one dimension, %s given', $value);
    }

    public static function forMismatchedDimensions(mixed $value): self
    {
        return self::create('Both cube corners must have the same number of dimensions, %s given', $value);
    }
}
