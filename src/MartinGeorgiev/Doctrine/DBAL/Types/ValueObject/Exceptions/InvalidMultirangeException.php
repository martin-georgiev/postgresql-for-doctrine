<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

/**
 * Exception thrown when creating or manipulating Multirange value objects with invalid data.
 *
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class InvalidMultirangeException extends \InvalidArgumentException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidFormat(mixed $value): self
    {
        return self::create('Invalid multirange format: %s', $value);
    }

    public static function forEmptyRangeSegment(mixed $value): self
    {
        return self::create('Invalid multirange format, it holds an empty range segment: %s', $value);
    }

    public static function forUnbalancedBrackets(mixed $value): self
    {
        return self::create('Invalid multirange format, its brackets are unbalanced: %s', $value);
    }
}
