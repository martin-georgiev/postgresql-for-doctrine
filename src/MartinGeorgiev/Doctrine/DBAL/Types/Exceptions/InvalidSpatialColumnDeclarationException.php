<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

/**
 * Extends \LogicException because this signals a schema-mapping error caught while
 * generating DDL, not a per-value runtime conversion failure. Using ConversionException
 * would mislead consumers who catch it expecting recoverable per-row failures.
 *
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class InvalidSpatialColumnDeclarationException extends \LogicException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidGeometryType(mixed $value): self
    {
        return self::create('Column option "geometry_type" must be a known PostGIS geometry type, optionally suffixed with Z, M or ZM, %s given', $value);
    }

    public static function forInvalidSrid(mixed $value): self
    {
        return self::create('Column option "srid" must be a non-negative integer, %s given', $value);
    }
}
