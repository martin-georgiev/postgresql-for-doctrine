<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidFloatArrayItemForPHPException extends ConversionException
{
    private static function create(string $message, mixed $value, string $type): self
    {
        return new self(\sprintf($message, \var_export($value, true), $type));
    }

    public static function forValueThatIsNotAValidPHPFloat(mixed $value, string $type): self
    {
        return self::create('Given value of %s content cannot be transformed to valid PHP float from PostgreSQL %s type', $value, $type);
    }
}
