<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class InvalidGeometryForDatabaseException extends ConversionException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidType(mixed $value): self
    {
        return self::create('Database value must be a string, %s given', $value);
    }

    public static function forInvalidFormat(mixed $value): self
    {
        return self::create('Invalid geometry format in database: %s', $value);
    }
}
