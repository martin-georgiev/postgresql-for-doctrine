<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

use Doctrine\DBAL\Types\ConversionException;

/**
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidCompositeForDatabaseException extends ConversionException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidType(mixed $value): self
    {
        return self::create('Database value must be an array keyed by field name, %s given', $value);
    }

    public static function forMissingFields(string $missingFieldNames): self
    {
        return new self('Composite value must define every declared field, missing: '.$missingFieldNames);
    }

    public static function forUnknownFields(string $unknownFieldNames): self
    {
        return new self('Composite value must not contain undeclared fields, unknown: '.$unknownFieldNames);
    }

    public static function forUnsupportedFieldValue(string $fieldName, mixed $value): self
    {
        return self::create('Field '.$fieldName.' must convert to a scalar, %s given', $value);
    }
}
