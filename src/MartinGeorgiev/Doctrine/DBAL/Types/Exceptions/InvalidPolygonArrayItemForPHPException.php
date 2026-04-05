<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidPolygonArrayItemForPHPException extends ConversionException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidFormat(mixed $value): self
    {
        return self::create('Cannot convert polygon array item to PHP value. Value %s is not a valid polygon.', $value);
    }

    public static function forInvalidArrayType(mixed $value): self
    {
        return self::create('Cannot convert polygon array to PHP value. Expected a string or null, got %s.', $value);
    }
}
