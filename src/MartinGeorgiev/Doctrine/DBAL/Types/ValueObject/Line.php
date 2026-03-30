<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidLineException;

/**
 * Represents a PostgreSQL line geometric type.
 *
 * Format: {A,B,C} — infinite line defined by equation Ax + By + C = 0.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-LINE
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final readonly class Line implements \Stringable
{
    private const LINE_REGEX = '/^\{\s*-?\d+(?:\.\d+)?\s*,\s*-?\d+(?:\.\d+)?\s*,\s*-?\d+(?:\.\d+)?\s*\}$/';

    public function __construct(
        private string $value,
    ) {
        if (!\preg_match(self::LINE_REGEX, $value)) {
            throw InvalidLineException::forInvalidFormat($value);
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
