<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

/**
 * Exception thrown when creating or manipulating Range value objects with invalid data.
 *
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class InvalidRangeException extends \InvalidArgumentException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidFormat(mixed $value): self
    {
        return self::create('Invalid range format: %s', $value);
    }

    public static function forInvalidNumericBound(mixed $value): self
    {
        return self::create('Range bound must be numeric, %s given', $value);
    }

    public static function forInvalidIntegerBound(mixed $value): self
    {
        return self::create('Range bound must be an integer, %s given', $value);
    }

    public static function forInvalidDateTimeBound(mixed $value): self
    {
        return self::create('Range bound must be a DateTimeInterface instance, %s given', $value);
    }

    public static function forNonFiniteBound(mixed $value): self
    {
        return self::create('Range bound must be a number a PHP float can hold, %s given', $value);
    }

    public static function forBoundOutsideSubtypeRange(mixed $value, int $minimum, int $maximum): self
    {
        return new self(\sprintf(
            'Range bound must be within [%d, %d], %s given',
            $minimum,
            $maximum,
            \var_export($value, true)
        ));
    }

    public static function forUnparsableBound(mixed $value, \Throwable $throwable): self
    {
        return new self(
            \sprintf('Cannot parse range bound: %s', \var_export($value, true)),
            0,
            $throwable
        );
    }

    public static function forUnsupportedBoundedInfinity(string $rangeType): self
    {
        return self::create(
            'Bounded infinity is not supported for %s. Integer ranges do not have a concept of infinity in PostgreSQL. Use unbounded ranges (null bounds) instead.',
            $rangeType
        );
    }
}
