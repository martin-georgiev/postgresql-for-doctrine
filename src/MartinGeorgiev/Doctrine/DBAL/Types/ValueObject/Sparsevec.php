<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidSparsevecForPHPException;

/**
 * Value object representing a pgvector sparsevec value.
 *
 * The wire format is `{index:value,...}/dimensions` where indices are 1-based.
 *
 * @see https://github.com/pgvector/pgvector
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class Sparsevec implements \Stringable
{
    /**
     * @param array<int, float> $elements 1-based index => float value (only non-zero elements)
     * @param positive-int      $dimensions total number of dimensions
     */
    public function __construct(
        private readonly array $elements,
        private readonly int $dimensions,
    ) {
    }

    /**
     * @return array<int, float>
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @return positive-int
     */
    public function getDimensions(): int
    {
        return $this->dimensions;
    }

    public function __toString(): string
    {
        $parts = [];
        foreach ($this->elements as $index => $value) {
            $parts[] = $index.':'.$value;
        }

        return '{'.implode(',', $parts).'}'.'/'.$this->dimensions;
    }

    /**
     * @throws InvalidSparsevecForPHPException if the string format is invalid
     */
    public static function fromString(string $value): static
    {
        if (!\preg_match('/^\{(.*)\}\/(\d+)$/', $value, $matches)) {
            throw InvalidSparsevecForPHPException::forInvalidFormat($value);
        }

        $dimensionsRaw = $matches[2];
        $dimensions = (int) $dimensionsRaw;

        if ($dimensions <= 0) {
            throw InvalidSparsevecForPHPException::forInvalidFormat($value);
        }

        $elementsRaw = $matches[1];
        $elements = [];

        if ($elementsRaw !== '') {
            $pairs = \explode(',', $elementsRaw);
            foreach ($pairs as $pair) {
                if (!\preg_match('/^(\d+):(.+)$/', $pair, $pairMatches)) {
                    throw InvalidSparsevecForPHPException::forInvalidFormat($value);
                }

                $index = (int) $pairMatches[1];
                $elementValue = $pairMatches[2];

                if (!\is_numeric($elementValue)) {
                    throw InvalidSparsevecForPHPException::forInvalidFormat($value);
                }

                $elements[$index] = (float) $elementValue;
            }
        }

        /** @var positive-int $dimensions */
        return new static($elements, $dimensions);
    }
}
