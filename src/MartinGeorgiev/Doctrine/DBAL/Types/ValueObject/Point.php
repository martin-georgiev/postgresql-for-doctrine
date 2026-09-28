<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidPointException;
use MartinGeorgiev\Utils\PostgresFloat;

/**
 * Represents a PostgreSQL point geometric type.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-GEOMETRIC-POINTS
 * @since 3.1
 *
 * @author Sébastien Jean <sebastien.jean76@gmail.com>
 */
final readonly class Point extends BaseGeometricValue
{
    /**
     * @var string
     */
    private const POINT_REGEX = '/^\(\s*('.PostgresFloat::PATTERN.')\s*,\s*('.PostgresFloat::PATTERN.')\s*\)$/';

    public function __construct(
        private float $x,
        private float $y,
    ) {}

    public function __toString(): string
    {
        return \sprintf('(%s,%s)', PostgresFloat::format($this->x), PostgresFloat::format($this->y));
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
            throw InvalidPointException::forInvalidFormat($pointString, self::POINT_REGEX);
        }

        return new self(PostgresFloat::parse($matches[1]), PostgresFloat::parse($matches[2]));
    }
}
