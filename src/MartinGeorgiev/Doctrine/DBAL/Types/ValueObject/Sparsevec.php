<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidSparsevecForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidSparsevecException;

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
final readonly class Sparsevec implements \Stringable
{
    /**
     * @var array<int, float>
     */
    private array $elements;

    /**
     * @param array<int, float> $elements 1-based index => float value (only non-zero elements)
     * @param positive-int $dimensions total number of dimensions
     *
     * @throws InvalidSparsevecException if dimensions is not positive, element keys are out of range, or values are not numeric
     */
    public function __construct(
        array $elements,
        private int $dimensions,
    ) {
        if ($dimensions <= 0) {
            throw InvalidSparsevecException::forNonPositiveDimensions($dimensions);
        }

        $normalized = [];
        foreach ($elements as $key => $value) {
            if ($key < 1 || $key > $dimensions) {
                throw InvalidSparsevecException::forElementKeyOutOfRange($key, $dimensions);
            }

            if (!\is_float($value) && !\is_int($value)) {
                throw InvalidSparsevecException::forInvalidElementValue($key, $value);
            }

            $normalized[$key] = (float) $value;
        }

        $this->elements = $normalized;
    }

    public function __toString(): string
    {
        $parts = [];
        foreach ($this->elements as $index => $value) {
            $parts[] = $index.':'.$value;
        }

        return '{'.\implode(',', $parts).'}/'.$this->dimensions;
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

    /**
     * @throws InvalidSparsevecForPHPException if the string format is invalid
     */
    public static function fromString(string $value): static
    {
        if (!\preg_match('/^\{(.*)\}\/(\d+)$/', $value, $matches)) {
            throw InvalidSparsevecForPHPException::forInvalidFormat($value);
        }

        $dimensions = (int) $matches[2];
        if ($dimensions <= 0) {
            throw InvalidSparsevecForPHPException::forInvalidFormat($value);
        }

        $rawElements = $matches[1];
        $elements = [];

        if ($rawElements !== '') {
            $pairs = \explode(',', $rawElements);
            foreach ($pairs as $pair) {
                if (!\preg_match('/^(\d+):(.+)$/', $pair, $pairMatches)) {
                    throw InvalidSparsevecForPHPException::forInvalidFormat($value);
                }

                $index = (int) $pairMatches[1];
                $elementValue = $pairMatches[2];

                if ($index < 1 || $index > $dimensions) {
                    throw InvalidSparsevecForPHPException::forInvalidFormat($value);
                }

                if (!\is_numeric($elementValue)) {
                    throw InvalidSparsevecForPHPException::forInvalidFormat($value);
                }

                $elements[$index] = (float) $elementValue;
            }
        }

        /* @var positive-int $dimensions */
        return new self($elements, $dimensions);
    }
}
