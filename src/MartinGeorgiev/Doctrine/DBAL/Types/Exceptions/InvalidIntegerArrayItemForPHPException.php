<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidIntegerArrayItemForPHPException extends ConversionException
{
    private static function create(string $message, mixed $value, string $type): self
    {
        return new self(\sprintf($message, \var_export($value, true), $type));
    }

    public static function forValueThatIsNotAValidPHPInteger(mixed $value, string $type): self
    {
        return self::create('Given value of %s content cannot be transformed to valid PHP integer from PostgreSQL %s type', $value, $type);
    }

    public static function forValueOutOfRangeInPHP(mixed $value, string $type): self
    {
        return self::create('Given value of %s is out of range for PHP integer but appears valid for PostgreSQL %s type', $value, $type);
    }

    public static function forValueOutOfRangeInDatabaseType(mixed $value, string $type): self
    {
        return self::create('Given value of %s is out of range for PostgreSQL %s type', $value, $type);
    }
}
