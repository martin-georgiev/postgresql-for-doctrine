<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidPathException;
use MartinGeorgiev\Utils\PostgresFloat;

/**
 * Represents a PostgreSQL path geometric type.
 *
 * Formats:
 * - Open path: [(x1,y1),(x2,y2),...]
 * - Closed path: ((x1,y1),(x2,y2),...)
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-GEOMETRIC-PATHS
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final readonly class Path extends BaseGeometricValue
{
    /** @var list<Point> */
    private array $points;

    public function __construct(
        private bool $isOpen,
        Point ...$points,
    ) {
        $this->points = \array_values($points);
    }

    public function __toString(): string
    {
        $pointStrings = \array_map(
            static fn (Point $point): string => \sprintf(
                '(%s,%s)',
                PostgresFloat::format($point->getX()),
                PostgresFloat::format($point->getY())
            ),
            $this->points
        );
        $inner = \implode(',', $pointStrings);

        return $this->isOpen ? '['.$inner.']' : '('.$inner.')';
    }

    /**
     * @return list<Point>
     */
    public function getPoints(): array
    {
        return $this->points;
    }

    public function isOpen(): bool
    {
        return $this->isOpen;
    }

    public static function fromString(string $value): self
    {
        $isOpen = \str_starts_with($value, '[');

        $points = $isOpen ? self::parsePointList($value, '[', ']') : self::parsePointList($value, '(', ')');
        if ($points === null) {
            throw InvalidPathException::forInvalidFormat($value, $isOpen ? '[(x1,y1),(x2,y2),...]' : '((x1,y1),(x2,y2),...)');
        }

        return new self($isOpen, ...$points);
    }
}
