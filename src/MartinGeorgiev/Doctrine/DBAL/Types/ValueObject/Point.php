<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidPointException;

/**
 * @since 3.1
 *
 * @author Sébastien Jean <sebastien.jean76@gmail.com>
 */
final readonly class Point extends BaseGeometricValue
{
    /**
     * @var string
     */
    private const POINT_REGEX = '/^\(\s*('.self::COORDINATE_PATTERN.')\s*,\s*('.self::COORDINATE_PATTERN.')\s*\)$/';

    public function __construct(
        private float $x,
        private float $y,
    ) {
        if (!\is_finite($x)) {
            throw InvalidPointException::forNonFiniteCoordinate($x);
        }

        if (!\is_finite($y)) {
            throw InvalidPointException::forNonFiniteCoordinate($y);
        }
    }

    public function __toString(): string
    {
        return \sprintf('(%s,%s)', $this->x, $this->y);
    }

    public function getX(): float
    {
        return $this->x;
    }

    public function getY(): float
    {
        return $this->y;
    }

    public static function fromString(string $pointString): self
    {
        if (!\preg_match(self::POINT_REGEX, $pointString, $matches)) {
            throw InvalidPointException::forInvalidPointFormat($pointString, self::POINT_REGEX);
        }

        return new self((float) $matches[1], (float) $matches[2]);
    }
}
