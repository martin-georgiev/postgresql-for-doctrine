<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidPolygonException;

/**
 * Represents a PostgreSQL polygon geometric type.
 *
 * Format: ((x1,y1),(x2,y2),...) — polygon defined by its vertices.
 *
 * @see https://www.postgresql.org/docs/current/datatype-geometric.html#DATATYPE-POLYGON
 *
 * @since 4.4
 */
final readonly class Polygon implements \Stringable
{
    private const POINT_PATTERN = '\(\s*-?\d+(?:\.\d+)?\s*,\s*-?\d+(?:\.\d+)?\s*\)';

    private const POLYGON_REGEX = '/^\(\s*'.self::POINT_PATTERN.'(?:\s*,\s*'.self::POINT_PATTERN.'){1,}\s*\)$/';

    public function __construct(
        private string $value,
    ) {
        if (!\preg_match(self::POLYGON_REGEX, $value)) {
            throw InvalidPolygonException::forInvalidFormat($value);
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }
}
