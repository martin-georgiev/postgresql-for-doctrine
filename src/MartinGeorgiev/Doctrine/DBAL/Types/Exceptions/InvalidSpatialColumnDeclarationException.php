<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

/**
 * Extends \LogicException because a bad `geometry_type` or `srid` column option is a
 * mapping mistake raised while building the SQL declaration, not a per-value conversion
 * failure. ConversionException would invite callers to catch it per row, where it can
 * never occur.
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
