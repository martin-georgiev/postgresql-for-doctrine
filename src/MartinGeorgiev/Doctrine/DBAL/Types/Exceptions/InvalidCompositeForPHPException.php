<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidCompositeForPHPException extends ConversionException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidType(mixed $value): self
    {
        return self::create('PHP value must be a string, %s given', $value);
    }

    public static function forInvalidFormat(string $value): self
    {
        return self::create('Invalid composite record format: %s', $value);
    }

    public static function forUnexpectedFieldCount(int $expected, int $actual, string $value): self
    {
        return self::create('Composite record must hold '.$expected.' fields, '.$actual.' found in: %s', $value);
    }
}
