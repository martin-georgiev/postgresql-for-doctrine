<?php

declare(strict_types=0);

namespace MartinGeorgiev\Model;

abstract class BaseRange implements RangeInterface
{
    public function isLowerInfinite(): bool
    {
        return null === $this->lower;
    }

    public function isUpperInfinite(): bool
    {
        return null === $this->upper;
    }

    public function isEmpty(): bool
    {
        return null !== $this->lower && $this->lower === $this->upper;
    }

    public function isInfinite(): bool
    {
        return $this->isLowerInfinite() && $this->isUpperInfinite();
    }

    public function hasSingleBoundary(): bool
    {
        return !$this->isLowerInfinite() xor !$this->isUpperInfinite();
    }

    public function hasBothBoundaries(): bool
    {
        return !$this->isLowerInfinite() && !$this->isUpperInfinite();
    }
}
