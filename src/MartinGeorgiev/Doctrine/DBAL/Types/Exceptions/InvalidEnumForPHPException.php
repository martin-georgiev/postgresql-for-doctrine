<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidEnumForPHPException extends ConversionException
{
    private static function create(string $message, mixed ...$values): self
    {
        $exported = \array_map(static fn (mixed $v): string => \var_export($v, true), $values);

        return new self(\sprintf($message, ...$exported));
    }

    public static function forInvalidType(mixed $value): self
    {
        return self::create('PHP value must be a string, %s given', $value);
    }

    public static function forNonBackedEnum(string $enumClass): self
    {
        return self::create('Class %s is not a BackedEnum', $enumClass);
    }

    public static function forUnknownValue(string $value, string $enumClass): self
    {
        return self::create('Value %s is not a valid case of enum %s', $value, $enumClass);
    }
}
