<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidPathException;

/**
 * Represents a PostgreSQL path geometric type.
 *
 * Formats:
 * - Open path: [(x1,y1),(x2,y2),...]
 * - Closed path: ((x1,y1),(x2,y2),...)
 *
 * @see https://www.postgresql.org/docs/current/datatype-geometric.html#id-1.5.7.16.9
 *
 * @since 4.4
 */
final readonly class Path implements \Stringable
{
    private const POINT_PATTERN = '\(\s*-?\d+(?:\.\d+)?\s*,\s*-?\d+(?:\.\d+)?\s*\)';

    private const OPEN_PATH_REGEX = '/^\[\s*'.self::POINT_PATTERN.'(?:\s*,\s*'.self::POINT_PATTERN.')*\s*\]$/';

    private const CLOSED_PATH_REGEX = '/^\(\s*'.self::POINT_PATTERN.'(?:\s*,\s*'.self::POINT_PATTERN.')*\s*\)$/';

    public function __construct(
        private string $value,
    ) {
        if (!\preg_match(self::OPEN_PATH_REGEX, $value) && !\preg_match(self::CLOSED_PATH_REGEX, $value)) {
            throw InvalidPathException::forInvalidFormat($value);
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
