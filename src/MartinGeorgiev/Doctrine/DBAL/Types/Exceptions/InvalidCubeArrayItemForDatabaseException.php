<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidCubeArrayItemForDatabaseException extends ConversionException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidType(mixed $value): self
    {
        return self::create('Cannot convert cube array item to database value. Expected a Cube value object, got %s.', $value);
    }
}
