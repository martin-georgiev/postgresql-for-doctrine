<?php

declare(strict_types=0);

namespace MartinGeorgiev\Model;

use MartinGeorgiev\Utils\MathUtils;

/**
 * @implements RangeInterface<float|int>
 */
class ArithmeticRange extends BaseRange
{
    public function __construct(
        public null|float|int $lower,
        public null|float|int $upper,
        public bool $lowerInclusive = true,
        public bool $upperInclusive = false,
    ) {
        // Void
    }

    public function __toString(): string
    {
        if (null !== $this->lower && $this->lower === $this->upper && !$this->lowerInclusive && !$this->upperInclusive) {
            return 'empty';
        }

        return \sprintf(
            '%s%s,%s%s',
            $this->lowerInclusive ? '[' : '(',
            $this->lower,
            $this->upper,
            $this->upperInclusive ? ']' : ')',
        );
    }

    public function contains(mixed $target): bool
    {
        return MathUtils::inRange($target, $this->lower, $this->upper, $this->lowerInclusive, $this->upperInclusive);
    }

    /**
     * @see https://www.postgresql.org/docs/current/rangetypes.html#RANGETYPES-INFINITE
     */
    public static function createFromString(string $value): self
    {
        if (!\preg_match('/([\[(])(.*),(.*)([])])/', $value, $matches)) {
            throw new \RuntimeException('Unexpected value: '.$value);
        }

        $startParenthesis = $matches[1];
        $startsAtString = \trim($matches[2], '"');
        $endsAtString = \trim($matches[3], '"');
        $endParenthesis = $matches[4];

        if (\in_array($startsAtString, ['infinity', '-infinity', ''], true)) {
            $startsAt = null;
        } else {
            $startsAt = MathUtils::stringToNumber($startsAtString);
        }

        if (\in_array($endsAtString, ['infinity', '-infinity', ''], true)) {
            $endsAt = null;
        } else {
            $endsAt = MathUtils::stringToNumber($endsAtString);
        }

        $startInclusive = '[' === $startParenthesis;
        $endInclusive = ']' === $endParenthesis;

        return new NumRange($startsAt, $endsAt, $startInclusive, $endInclusive);
    }
}
