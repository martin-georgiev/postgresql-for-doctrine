<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidCircleException;

/**
 * Represents a PostgreSQL circle geometric type.
 *
 * Format: <(x,y),r> — center point and radius.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-CIRCLE
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final readonly class Circle extends BaseGeometricValue
{
    /**
     * @var string
     */
    private const RADIUS_PATTERN = '\d+(?:\.\d+)?(?:[eE][+-]?\d+)?';

    /**
     * @var string
     */
    private const CIRCLE_REGEX = '/^<\s*\(\s*('.self::COORDINATE_PATTERN.')\s*,\s*('.self::COORDINATE_PATTERN.')\s*\)\s*,\s*('.self::RADIUS_PATTERN.')\s*>$/';

    public function __construct(
        private Point $center,
        private float $radius,
    ) {
        if ($radius < 0) {
            throw InvalidCircleException::forNegativeRadius($radius);
        }
    }

    public function __toString(): string
    {
        return \sprintf('<(%s,%s),%s>', $this->center->getX(), $this->center->getY(), $this->radius);
    }

    public function getCenter(): Point
    {
        return $this->center;
    }

    public function getRadius(): float
    {
        return $this->radius;
    }

    public static function fromString(string $value): self
    {
        if (!\preg_match(self::CIRCLE_REGEX, $value, $matches)) {
            throw InvalidCircleException::forInvalidFormat($value, self::CIRCLE_REGEX);
        }

        return new self(new Point((float) $matches[1], (float) $matches[2]), (float) $matches[3]);
    }
}
