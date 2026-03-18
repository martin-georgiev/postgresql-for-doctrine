<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 4.4
 */
final class InvalidIntervalArrayItemForDatabaseException extends ConversionException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidType(mixed $value): self
    {
        return self::create('Cannot convert interval array item to database value. Expected an IntervalValueObject, DateInterval, string, or null, got %s.', $value);
    }

    public static function forInvalidFormat(mixed $value): self
    {
        return self::create('Cannot convert interval array item to database value. Value %s is not a valid interval.', $value);
    }
}
