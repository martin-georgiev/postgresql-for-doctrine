<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidEnumArrayItemForPHPException extends ConversionException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidArrayType(mixed $value): self
    {
        return self::create('Value must be an array, %s given', $value);
    }

    public static function forInvalidFormat(mixed $value): self
    {
        return self::create('Invalid enum array format: %s', $value);
    }

    public static function forInvalidType(mixed $value): self
    {
        return self::create('Array items must be enum labels given as strings, %s given', $value);
    }

    public static function forNonBackedEnum(string $enumClass): self
    {
        return self::create('Class %s is not a BackedEnum', $enumClass);
    }

    public static function forUnknownValue(string $value, string $enumClass): self
    {
        return self::create('Array item %s is not a valid case of enum '.$enumClass, $value);
    }
}
