<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

/**
 * Thrown when an Interval cannot be built from a string.
 *
 * Extends \InvalidArgumentException, not ConversionException: fromString() has thrown that since the
 * type was added, and moving it would break existing catch blocks.
 *
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class InvalidIntervalException extends \InvalidArgumentException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forEmptyValue(mixed $value): self
    {
        return self::create('Interval value must be a non-empty string, %s given', $value);
    }

    public static function forInvalidFormat(mixed $value): self
    {
        return self::create('Cannot parse interval string: %s', $value);
    }

    public static function forInvalidIso8601Format(mixed $value): self
    {
        return self::create('Invalid ISO 8601 interval string: %s', $value);
    }

    public static function forOutOfRangeAmount(mixed $value): self
    {
        return self::create('Interval amount is out of range: %s', $value);
    }

    public static function forUnsupportedInfinity(mixed $value): self
    {
        return self::create('Infinite intervals cannot be represented by DateInterval, %s given', $value);
    }
}
