<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * @since 3.1
 *
 * @author Sébastien Jean <sebastien.jean76@gmail.com>
 */
final class Point implements \Stringable
{
    private const COORDINATE_PATTERN = '-?\d+(?:\.\d{1,6})?';

    private const POINT_REGEX = '/\(('.self::COORDINATE_PATTERN.'),\s*('.self::COORDINATE_PATTERN.')\)/';

    public function __construct(
        private readonly float $x,
        private readonly float $y,
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
            throw new \InvalidArgumentException(
                \sprintf('Invalid point format. Expected format matching %s, got: %s', self::POINT_REGEX, $pointString)
            );
        }

        return new self((float) $matches[1], (float) $matches[2]);
    }

    private function validateCoordinate(float $value, string $name): void
    {
        $stringValue = (string) $value;

        $floatRegex = '/^'.self::COORDINATE_PATTERN.'$/';
        if (!\preg_match($floatRegex, $stringValue)) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid %s coordinate format: %s', $name, $stringValue)
            );
        }
    }
}
