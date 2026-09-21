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
    /**
     * @var string
     */
    public const LOWER_BOUND = 'Lower';

    /**
     * @var string
     */
    public const UPPER_BOUND = 'Upper';

    /**
     * Used where the failure is not attributable to one end — a comparison, or a bound parsed out of a literal.
     *
     * @var string
     */
    private const ANY_BOUND = 'Range';

    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidFormat(mixed $value): self
    {
        return self::create('Invalid range format: %s', $value);
    }

    public static function forInvalidBoundType(string $expectedType, mixed $value, ?string $bound = null): self
    {
        return new self(\sprintf(
            '%s bound must be %s, %s given',
            $bound ?? self::ANY_BOUND,
            $expectedType,
            \var_export($value, true)
        ));
    }

    public static function forNonFiniteBound(mixed $value, ?string $bound = null): self
    {
        return new self(\sprintf(
            '%s bound must be a number a PHP float can hold, %s given',
            $bound ?? self::ANY_BOUND,
            \var_export($value, true)
        ));
    }

    public static function forBoundOutsideSubtypeRange(mixed $value, int $minimum, int $maximum, ?string $bound = null): self
    {
        return new self(\sprintf(
            '%s bound must be within [%d, %d], %s given',
            $bound ?? self::ANY_BOUND,
            $minimum,
            $maximum,
            \var_export($value, true)
        ));
    }

    public static function forUnparsableBound(mixed $value, \Throwable $throwable): self
    {
        return new self(
            \sprintf('Cannot parse range bound: %s. Error: %s', \var_export($value, true), $throwable->getMessage()),
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
