<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidLsegException;

/**
 * Represents a PostgreSQL lseg (line segment) geometric type.
 *
 * Format: [(x1,y1),(x2,y2)] — finite line segment defined by two endpoints.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-LSEG
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final readonly class Lseg implements \Stringable
{
    private const LSEG_REGEX = '/^\[?\s*\(\s*-?\d+(?:\.\d+)?\s*,\s*-?\d+(?:\.\d+)?\s*\)\s*,\s*\(\s*-?\d+(?:\.\d+)?\s*,\s*-?\d+(?:\.\d+)?\s*\)\s*\]?$/';

    public function __construct(
        private string $value,
    ) {
        if (!\preg_match(self::LSEG_REGEX, $value)) {
            throw InvalidLsegException::forInvalidFormat($value);
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
