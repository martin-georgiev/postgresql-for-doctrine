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
final readonly class Polygon implements \Stringable
{
    private const COORDINATE_PATTERN = '-?\d+(?:\.\d{1,6})?';

    private const POINT_PATTERN = '\(\s*'.self::COORDINATE_PATTERN.'\s*,\s*'.self::COORDINATE_PATTERN.'\s*\)';

    private const POLYGON_REGEX = '/^\(\s*'.self::POINT_PATTERN.'(?:\s*,\s*'.self::POINT_PATTERN.'){1,}\s*\)$/';

    private const POINT_CAPTURE_REGEX = '/\(('.self::COORDINATE_PATTERN.'),\s*('.self::COORDINATE_PATTERN.')\)/';

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
            static fn (Point $vertex): string => \sprintf('(%s,%s)', $vertex->getX(), $vertex->getY()),
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

        \preg_match_all(self::POINT_CAPTURE_REGEX, $value, $matches, PREG_SET_ORDER);

        $vertices = [];
        foreach ($matches as $match) {
            $vertices[] = new Point((float) $match[1], (float) $match[2]);
        }

        return new self(...$vertices);
    }
}
