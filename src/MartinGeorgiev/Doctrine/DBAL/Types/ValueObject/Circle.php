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
final readonly class Circle implements \Stringable
{
    private const COORDINATE_PATTERN = '-?\d+(?:\.\d{1,6})?';

    private const CIRCLE_REGEX = '/^<\(('.self::COORDINATE_PATTERN.'),('.self::COORDINATE_PATTERN.')\),('.self::COORDINATE_PATTERN.')>$/';

    public function __construct(
        private Point $center,
        private float $radius,
    ) {
        $this->validateRadius($radius);
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

        return new self(
            new Point((float) $matches[1], (float) $matches[2]),
            (float) $matches[3]
        );
    }

    private function validateRadius(float $value): void
    {
        $stringValue = (string) $value;

        $floatRegex = '/^'.self::COORDINATE_PATTERN.'$/';
        if (!\preg_match($floatRegex, $stringValue)) {
            throw InvalidCircleException::forInvalidCoordinate('radius', $stringValue);
        }
    }
}
