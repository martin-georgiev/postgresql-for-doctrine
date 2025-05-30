<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 3.1
 *
 * @author Sébastien Jean <sebastien.jean76@gmail.com>
 */
class InvalidPointArrayItemForPHPException extends ConversionException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidType(mixed $value): self
    {
        return self::create('Array values must be strings, %s given', $value);
    }

    public static function forInvalidFormat(mixed $value): self
    {
        return self::create('Invalid point format in array: %s', $value);
    }

    public static function forInvalidArrayType(mixed $value): self
    {
        return self::create('Value must be an array, %s given', $value);
    }
}
