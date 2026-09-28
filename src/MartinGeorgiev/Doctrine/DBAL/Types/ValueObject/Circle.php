<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidCircleException;
use MartinGeorgiev\Utils\PostgresFloat;

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
     * PostgreSQL accepts a NaN or Infinity radius but rejects every negative one, -Infinity included, so this pattern
     * stays unsigned while allowing the non-finite spellings.
     *
     * @var string
     */
    private const RADIUS_PATTERN = PostgresFloat::UNSIGNED_PATTERN;

    /**
     * @var string
     */
    private const CIRCLE_REGEX = '/^<\s*\(\s*('.PostgresFloat::PATTERN.')\s*,\s*('.PostgresFloat::PATTERN.')\s*\)\s*,\s*('.self::RADIUS_PATTERN.')\s*>$/';

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
        return \sprintf(
            '<(%s,%s),%s>',
            PostgresFloat::format($this->center->getX()),
            PostgresFloat::format($this->center->getY()),
            PostgresFloat::format($this->radius)
        );
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

        return new self(
            new Point(PostgresFloat::parse($matches[1]), PostgresFloat::parse($matches[2])),
            PostgresFloat::parse($matches[3])
        );
    }
}
