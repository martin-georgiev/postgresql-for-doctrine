<?php

declare(strict_types=1);

namespace MartinGeorgiev\Model;

/**
 * @template T of int|float|\DateTimeInterface
 */
interface RangeInterface extends \Stringable
{
    /**
     * @param T $target
     */
    public function contains(mixed $target): bool;

    public function isLowerInfinite(): bool;

    public function isUpperInfinite(): bool;

    public function isEmpty(): bool;

    public function isInfinite(): bool;

    public function hasSingleBoundary(): bool;

    public function hasBothBoundaries(): bool;
}
