<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

/**
 * Abstract base for PostgreSQL multirange value objects.
 *
 * A multirange is an ordered list of non-overlapping, non-adjacent ranges.
 * PostgreSQL format: {[1,3),[7,10)} or {} for empty.
 *
 * @template R of Range
 *
 * @phpstan-consistent-constructor
 *
 * @since 4.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class Multirange implements \Stringable
{
    /**
     * @param R[] $ranges
     */
    public function __construct(
        protected readonly array $ranges = [],
    ) {}

    public function __toString(): string
    {
        if ($this->ranges === []) {
            return '{}';
        }

        return '{'.\implode(',', \array_map(static fn (Range $range): string => (string) $range, $this->ranges)).'}';
    }

    /**
     * @return R[]
     */
    public function getRanges(): array
    {
        return $this->ranges;
    }

    public function isEmpty(): bool
    {
        return $this->ranges === [];
    }

    public static function fromString(string $value): static
    {
        $value = \trim($value);

        if ($value === '{}') {
            return new static([]);
        }

        if (!\str_starts_with($value, '{') || !\str_ends_with($value, '}')) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid multirange format: %s', $value)
            );
        }

        $inner = \substr($value, 1, -1);
        $ranges = static::splitRanges($inner);

        return new static(\array_map(static::parseRange(...), $ranges));
    }

    /**
     * Split the inner multirange string into individual range strings.
     * Handles both formats: "[1,3),[7,10)" and "empty,[7,10)".
     *
     * @return string[]
     */
    protected static function splitRanges(string $inner): array
    {
        $ranges = [];
        $depth = 0;
        $current = '';

        for ($i = 0, $len = \strlen($inner); $i < $len; $i++) {
            $char = $inner[$i];

            if ($char === '[' || $char === '(') {
                $depth++;
                $current .= $char;
            } elseif ($char === ']' || $char === ')') {
                $depth--;
                $current .= $char;
            } elseif ($char === ',' && $depth === 0) {
                if ($current !== '') {
                    $ranges[] = \trim($current);
                }

                $current = '';
            } else {
                $current .= $char;
            }
        }

        if ($current !== '') {
            $ranges[] = \trim($current);
        }

        return $ranges;
    }

    /**
     * @return R
     */
    abstract protected static function parseRange(string $rangeString): Range;
}
