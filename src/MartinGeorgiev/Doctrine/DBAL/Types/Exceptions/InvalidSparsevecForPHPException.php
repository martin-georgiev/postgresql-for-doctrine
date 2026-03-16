<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class InvalidSparsevecForPHPException extends ConversionException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidType(mixed $value): self
    {
        return self::create('Value must be a sparsevec string, %s given', \gettype($value));
    }

    public static function forInvalidFormat(mixed $value): self
    {
        return self::create('Invalid sparsevec format: %s', $value);
    }
}
