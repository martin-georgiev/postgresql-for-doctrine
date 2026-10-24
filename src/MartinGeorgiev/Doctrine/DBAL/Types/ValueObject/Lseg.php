<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidLsegException;
use MartinGeorgiev\Utils\PostgresFloat;

/**
 * Represents a PostgreSQL lseg (line segment) geometric type.
 *
 * Format: [(x1,y1),(x2,y2)] — finite line segment defined by two endpoints.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-LSEG
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final readonly class Lseg extends BaseGeometricValue
{
    public function __construct(
        private Point $start,
        private Point $end,
    ) {}

    public function __toString(): string
    {
        return \sprintf(
            '[(%s,%s),(%s,%s)]',
            PostgresFloat::format($this->start->getX()),
            PostgresFloat::format($this->start->getY()),
            PostgresFloat::format($this->end->getX()),
            PostgresFloat::format($this->end->getY())
        );
    }

    public function getStart(): Point
    {
        return $this->start;
    }

    public function getEnd(): Point
    {
        return $this->end;
    }

    public static function fromString(string $value): self
    {
        $points = \str_starts_with($value, '[') ? self::parsePointList($value, '[', ']', 2) : self::parsePointList($value, maxPoints: 2);
        if ($points === null || \count($points) !== 2) {
            throw InvalidLsegException::forInvalidFormat($value, '[(x1,y1),(x2,y2)]');
        }

        return new self($points[0], $points[1]);
    }
}
