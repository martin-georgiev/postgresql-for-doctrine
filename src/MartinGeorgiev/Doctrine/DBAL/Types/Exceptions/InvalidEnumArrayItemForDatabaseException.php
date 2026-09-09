<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidEnumArrayItemForDatabaseException extends ConversionException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidType(mixed $value): self
    {
        return self::create('Array items must be BackedEnum instances or null, %s given', $value);
    }

    public static function forWrongEnumClass(\BackedEnum $backedEnum, string $expectedClass): self
    {
        return self::create('Array items must be instances of '.$expectedClass.', %s given', $backedEnum);
    }
}
