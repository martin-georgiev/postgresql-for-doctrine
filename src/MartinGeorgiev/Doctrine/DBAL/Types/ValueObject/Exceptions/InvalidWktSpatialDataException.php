<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\GeometryType;

/**
 * Exception thrown when creating or manipulating WktSpatialData value objects with invalid data.
 *
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class InvalidWktSpatialDataException extends \InvalidArgumentException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forEmptyWkt(): self
    {
        return new self('Empty Wkt string provided');
    }

    public static function forMissingSemicolonInEwkt(): self
    {
        return new self('Invalid Ewkt: missing semicolon after Srid prefix');
    }

    public static function forInvalidSridValue(mixed $sridValue): self
    {
        return self::create('Invalid Srid value in Ewkt: %s', $sridValue);
    }

    public static function forInvalidFormat(string $wkt): self
    {
        return self::create('Invalid Wkt format: %s', $wkt);
    }

    public static function forEmptyCoordinateSection(): self
    {
        return new self('Invalid Wkt: empty coordinate/body section');
    }

    public static function forUnsupportedGeometryType(string $type): self
    {
        $supportedTypes = \array_map(static fn (GeometryType $geometryType) => $geometryType->value, GeometryType::cases());

        return new self(\sprintf(
            'Unsupported geometry type: %s. Supported types: %s',
            \var_export($type, true),
            \implode(', ', $supportedTypes)
        ));
    }
}
