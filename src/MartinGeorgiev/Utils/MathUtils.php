<?php

declare(strict_types=1);

namespace MartinGeorgiev\Utils;

class MathUtils
{
    /**
     * Decides whether the provided $value is in a range delimited by $start and $end values.
     *
     * - If $start is null, then the comparison is "lesser than $end" only
     * - If $end is null, the comparison is "greater than $start" only
     * - The $(start|end)Inclusive determine whether the comparison is "lesser/greater than", or "lesser/greater or equal than"
     */
    public static function inRange(
        null|float|int $value,
        null|float|int $start = null,
        null|float|int $end = null,
        bool $startInclusive = true,
        bool $endInclusive = false,
    ): bool {
        if (null === $value) {
            return false;
        }

        if (null !== $start && null !== $end && (float) $start === (float) $end) {
            return (float) $value === (float) $start;
        }

        if (null === $start) {
            $startInclusive = true;
        }

        if (null === $end) {
            $endInclusive = true;
        }

        // Depending on this->range[Start/End]Inclusive, we will use (>= or >) and (<= or <) to work out where the value is
        $isGreater = $startInclusive ? $value >= $start : $value > $start;
        $isLesser = $endInclusive ? $value <= $end : $value < $end;

        return
            (null === $start || $isGreater)
            && (null === $end || $isLesser);
    }

    public static function stringToNumber(?string $number): null|float|int
    {
        if (!\is_numeric($number)) {
            return null;
        }

        return ((float) $number == (int) $number) ? (int) $number : (float) $number;
    }
}
