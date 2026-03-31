<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidPathException;

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
final readonly class Path implements \Stringable
{
    private const COORDINATE_PATTERN = '-?\d+(?:\.\d{1,6})?';

    private const POINT_PATTERN = '\(\s*'.self::COORDINATE_PATTERN.'\s*,\s*'.self::COORDINATE_PATTERN.'\s*\)';

    private const OPEN_PATH_REGEX = '/^\[\s*'.self::POINT_PATTERN.'(?:\s*,\s*'.self::POINT_PATTERN.')*\s*\]$/';

    private const CLOSED_PATH_REGEX = '/^\(\s*'.self::POINT_PATTERN.'(?:\s*,\s*'.self::POINT_PATTERN.')*\s*\)$/';

    private const POINT_CAPTURE_REGEX = '/\(('.self::COORDINATE_PATTERN.'),\s*('.self::COORDINATE_PATTERN.')\)/';

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
            static fn (Point $point): string => \sprintf('(%s,%s)', $point->getX(), $point->getY()),
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

        $regex = $isOpen ? self::OPEN_PATH_REGEX : self::CLOSED_PATH_REGEX;
        if (!\preg_match($regex, $value)) {
            throw InvalidPathException::forInvalidFormat($value, $regex);
        }

        \preg_match_all(self::POINT_CAPTURE_REGEX, $value, $matches, PREG_SET_ORDER);

        $points = [];
        foreach ($matches as $match) {
            $points[] = new Point((float) $match[1], (float) $match[2]);
        }

        return new self($isOpen, ...$points);
    }
}
