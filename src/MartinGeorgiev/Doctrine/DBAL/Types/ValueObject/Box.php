<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidBoxException;

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
final readonly class Box implements \Stringable
{
    private const COORDINATE_PATTERN = '-?\d+(?:\.\d{1,6})?';

    private const BOX_REGEX = '/^\(\s*('.self::COORDINATE_PATTERN.')\s*,\s*('.self::COORDINATE_PATTERN.')\s*\)\s*,\s*\(\s*('.self::COORDINATE_PATTERN.')\s*,\s*('.self::COORDINATE_PATTERN.')\s*\)$/';

    public function __construct(
        private Point $upperRight,
        private Point $lowerLeft,
    ) {}

    public function __toString(): string
    {
        return \sprintf(
            '(%s,%s),(%s,%s)',
            $this->upperRight->getX(),
            $this->upperRight->getY(),
            $this->lowerLeft->getX(),
            $this->lowerLeft->getY()
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
        if (!\preg_match(self::BOX_REGEX, $value, $matches)) {
            throw InvalidBoxException::forInvalidFormat($value, self::BOX_REGEX);
        }

        return new self(
            new Point((float) $matches[1], (float) $matches[2]),
            new Point((float) $matches[3], (float) $matches[4])
        );
    }
}
