<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 3.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidIntegerArrayItemForDatabaseException extends ConversionException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function isNotANumber(mixed $value): self
    {
        return self::create('Given value of %s is not a number.', $value);
    }

    public static function doesNotMatchRegex(mixed $value): self
    {
        return self::create('Given value of %s does not match integer regex.', $value);
    }

    public static function isBelowMinValue(mixed $value): self
    {
        return self::create('Given value of %s is below minimum value.', $value);
    }

    public static function isAboveMaxValue(mixed $value): self
    {
        return self::create('Given value of %s is above maximum value.', $value);
    }

    public static function isOutOfRange(mixed $value): self
    {
        return self::create('Given value of %s is out of range.', $value);
    }
}
