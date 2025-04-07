<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 3.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidPointArrayItemForDatabaseException extends ConversionException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function isNotAPoint(mixed $value): self
    {
        return self::create('Given value of %s is not a point.', $value);
    }
}
