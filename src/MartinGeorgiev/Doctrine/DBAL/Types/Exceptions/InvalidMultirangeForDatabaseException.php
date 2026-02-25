<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidMultirangeForDatabaseException extends ConversionException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidType(mixed $value): self
    {
        return self::create('Invalid PHP value type for multirange conversion. Expected Multirange instance, %s given', $value);
    }
}
