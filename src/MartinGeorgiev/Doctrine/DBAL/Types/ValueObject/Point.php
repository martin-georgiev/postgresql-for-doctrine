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
    public function __construct(
        private readonly float $x,
        private readonly float $y,
    ) {}

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
}
