<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidRangeForPHPException extends ConversionException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
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

    public static function forInvalidType(mixed $value): self
    {
        return self::create('Invalid database value type for range conversion. Expected string, %s given', $value);
    }

    public static function forInvalidFormat(string $value): self
    {
        return new self(\sprintf('Invalid range format from database: %s', $value));
    }

    public static function forUnsupportedBoundedInfinity(string $rangeType): self
    {
        return new self(
            \sprintf(
                'Bounded infinity is not supported for %s. Integer ranges do not have a concept of infinity in PostgreSQL. Use unbounded ranges (null bounds) instead.',
                $rangeType
            )
        );
    }
}
