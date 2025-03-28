<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

class InvalidJsonbArrayItemForPHPException extends ConversionException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidType(mixed $value): self
    {
        return self::create('Array values must be valid JSON objects, %s given', $value);
    }

    public static function forInvalidFormat(mixed $value): self
    {
        return self::create('Invalid JSONB format in array: %s', $value);
    }
}
