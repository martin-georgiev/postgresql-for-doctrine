<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidCircleException;

/**
 * Represents a PostgreSQL circle geometric type.
 *
 * Format: <(x,y),r> — center point and radius.
 *
 * @see https://www.postgresql.org/docs/current/datatype-geometric.html#DATATYPE-CIRCLE
 *
 * @since 4.4
 */
final readonly class Circle implements \Stringable
{
    private const CIRCLE_REGEX = '/^<\s*\(\s*-?\d+(?:\.\d+)?\s*,\s*-?\d+(?:\.\d+)?\s*\)\s*,\s*-?\d+(?:\.\d+)?\s*>$/';

    public function __construct(
        private string $value,
    ) {
        if (!\preg_match(self::CIRCLE_REGEX, $value)) {
            throw InvalidCircleException::forInvalidFormat($value);
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }
}
