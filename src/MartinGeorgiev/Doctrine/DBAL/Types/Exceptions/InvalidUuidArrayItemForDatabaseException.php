<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidUuidArrayItemForDatabaseException extends ConversionException
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
        return self::create('Invalid UUID format in array: %s', $value);
    }
}
