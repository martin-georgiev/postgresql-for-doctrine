<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidLineArrayItemForPHPException extends ConversionException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidFormat(mixed $value): self
    {
        return self::create('Invalid line format in array: %s', $value);
    }

    public static function forInvalidArrayType(mixed $value): self
    {
        return self::create('Value must be an array, %s given', $value);
    }
}
