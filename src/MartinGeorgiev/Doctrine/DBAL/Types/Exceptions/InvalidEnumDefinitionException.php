<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

/**
 * Extends \LogicException because this signals a mismatch between a DBAL type
 * subclass and the PHP enum it maps, caught while generating DDL, not a per-value
 * runtime conversion failure. Using ConversionException would mislead consumers who
 * catch it expecting recoverable per-row failures.
 *
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class InvalidEnumDefinitionException extends \LogicException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidTypeName(mixed $value): self
    {
        return self::create('Enum type name must be a PostgreSQL identifier, optionally schema-qualified, %s given', $value);
    }

    public static function forNonStringCaseValue(mixed $value): self
    {
        return self::create('PostgreSQL enum labels are text, so only string-backed enums can be declared, %s given', $value);
    }
}
