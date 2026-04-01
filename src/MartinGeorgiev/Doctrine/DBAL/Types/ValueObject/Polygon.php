<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidPolygonException;

/**
 * Represents a PostgreSQL polygon geometric type.
 *
 * Format: ((x1,y1),(x2,y2),...) — polygon defined by its vertices.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-POLYGON
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final readonly class Polygon extends BaseGeometricValue
{
    /**
     * @var string
     */
    private const POLYGON_REGEX = '/^\(\s*'.self::POINT_PATTERN.'(?:\s*,\s*'.self::POINT_PATTERN.'){1,}\s*\)$/';

    /** @var list<Point> */
    private array $vertices;

    public function __construct(
        Point ...$vertices,
    ) {
        if (\count($vertices) < 2) {
            throw InvalidPolygonException::forTooFewVertices(\count($vertices));
        }

        $this->vertices = \array_values($vertices);
    }

    public function __toString(): string
    {
        $vertexStrings = \array_map(
            static fn (Point $point): string => \sprintf('(%s,%s)', $point->getX(), $point->getY()),
            $this->vertices
        );

        return '('.\implode(',', $vertexStrings).')';
    }

    /**
     * @return list<Point>
     */
    public function getVertices(): array
    {
        return $this->vertices;
    }

    public static function fromString(string $value): self
    {
        if (!\preg_match(self::POLYGON_REGEX, $value)) {
            throw InvalidPolygonException::forInvalidFormat($value, self::POLYGON_REGEX);
        }

        return new self(...self::extractPoints($value));
    }
}
