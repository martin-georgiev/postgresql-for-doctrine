<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidBoxException;
use MartinGeorgiev\Utils\PostgresFloat;

/**
 * Represents a PostgreSQL box geometric type.
 *
 * Format: (x1,y1),(x2,y2) — upper-right and lower-left corners.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-GEOMETRIC-BOXES
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final readonly class Box extends BasePointListValue
{
    public function __construct(
        private Point $upperRight,
        private Point $lowerLeft,
    ) {}

    public function __toString(): string
    {
        return \sprintf(
            '(%s,%s),(%s,%s)',
            PostgresFloat::format($this->upperRight->getX()),
            PostgresFloat::format($this->upperRight->getY()),
            PostgresFloat::format($this->lowerLeft->getX()),
            PostgresFloat::format($this->lowerLeft->getY())
        );
    }

    public function getUpperRight(): Point
    {
        return $this->upperRight;
    }

    public function getLowerLeft(): Point
    {
        return $this->lowerLeft;
    }

    public static function fromString(string $value): self
    {
        $points = self::parsePointList($value, exactPointCount: 2);
        if ($points === null) {
            throw InvalidBoxException::forInvalidFormat($value, '(x1,y1),(x2,y2)');
        }

        return new self($points[0], $points[1]);
    }
}
