<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidBoxException;

/**
 * Represents a PostgreSQL box geometric type.
 *
 * Format: (x1,y1),(x2,y2) — upper-right and lower-left corners.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-GEOMETRIC-BOXES
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final readonly class Box implements \Stringable
{
    private const BOX_REGEX = '/^\(?\s*-?\d+(?:\.\d+)?\s*,\s*-?\d+(?:\.\d+)?\s*\)\s*,\s*\(?\s*-?\d+(?:\.\d+)?\s*,\s*-?\d+(?:\.\d+)?\s*\)?$/';

    public function __construct(
        private string $value,
    ) {
        if (!\preg_match(self::BOX_REGEX, $value)) {
            throw InvalidBoxException::forInvalidFormat($value);
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $value): static
    {
        return new self($value);
    }
}
