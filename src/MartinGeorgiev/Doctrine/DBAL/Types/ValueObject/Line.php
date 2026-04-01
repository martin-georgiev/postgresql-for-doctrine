<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidLineException;

/**
 * Represents a PostgreSQL line geometric type.
 *
 * Format: {A,B,C} — infinite line defined by equation Ax + By + C = 0.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-LINE
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final readonly class Line extends BaseGeometricValue
{
    private const LINE_REGEX = '/^\{\s*('.self::COORDINATE_PATTERN.'),\s*('.self::COORDINATE_PATTERN.'),\s*('.self::COORDINATE_PATTERN.')\s*\}$/';

    public function __construct(
        private float $a,
        private float $b,
        private float $c,
    ) {
        if ($a == 0 && $b == 0) {
            throw InvalidLineException::forDegenerateLine();
        }
    }

    public function __toString(): string
    {
        return \sprintf('{%s,%s,%s}', (string) $this->a, (string) $this->b, (string) $this->c);
    }

    public function getA(): float
    {
        return $this->a;
    }

    public function getB(): float
    {
        return $this->b;
    }

    public function getC(): float
    {
        return $this->c;
    }

    public static function fromString(string $value): self
    {
        if (!\preg_match(self::LINE_REGEX, $value, $matches)) {
            throw InvalidLineException::forInvalidFormat($value, self::LINE_REGEX);
        }

        return new self((float) $matches[1], (float) $matches[2], (float) $matches[3]);
    }
}
