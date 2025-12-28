<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidPointException;

/**
 * @since 3.1
 *
 * @author Sébastien Jean <sebastien.jean76@gmail.com>
 */
final readonly class Point implements \Stringable
{
    private const COORDINATE_PATTERN = '-?\d+(?:\.\d{1,6})?';

    private const POINT_REGEX = '/\(('.self::COORDINATE_PATTERN.'),\s*('.self::COORDINATE_PATTERN.')\)/';

    public function __construct(
        private float $x,
        private float $y,
    ) {
        $this->validateCoordinate($x, 'x');
        $this->validateCoordinate($y, 'y');
    }

    public function __toString(): string
    {
        return \sprintf('(%f, %f)', $this->x, $this->y);
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

    private function validateCoordinate(float $value, string $name): void
    {
        $stringValue = (string) $value;

        $floatRegex = '/^'.self::COORDINATE_PATTERN.'$/';
        if (!\preg_match($floatRegex, $stringValue)) {
            throw InvalidPointException::forInvalidCoordinate($name, $stringValue);
        }
    }
}
