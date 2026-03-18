<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidIntervalForDatabaseException extends ConversionException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidType(mixed $value): self
    {
        return self::create('Value must be an Interval object, a DateInterval, a string, or null, %s given', $value);
    }

    public static function forInvalidFormat(mixed $value): self
    {
        return self::create('Value %s is not a valid interval', $value);
    }
}
