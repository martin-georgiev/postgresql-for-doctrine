<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidCubeException;

/**
 * Represents a PostgreSQL cube value, provided by the cube extension.
 *
 * A cube is either of:
 * - a point, written as a single coordinate group, e.g. (1, 2, 3); or
 * - a box spanned by two opposite corners, e.g. (1, 2, 3),(4, 5, 6).
 *
 * PostgreSQL preserves the corners order and collapses a zero-volume box back into a point,
 * so this value object normalizes the same way: equal corners are stored as a point and never re-emitted as a box.
 *
 * @see https://www.postgresql.org/docs/18/cube.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final readonly class Cube implements \Stringable
{
    /**
     * PostgreSQL rejects anything above this with "A cube cannot have more than 100 dimensions".
     */
    private const MAX_DIMENSIONS = 100;

    /**
     * PostgreSQL accepts non-finite coordinates in several spellings (nan, inf, -inf, infinity).
     * PostgreSQL always emits these as NaN, Infinity or -Infinity.
     *
     * @var string
     */
    private const COORDINATE_PATTERN = '(?:[+-]?(?:\d+(?:\.\d*)?|\.\d+)(?:[eE][+-]?\d+)?|[+-]?(?i:inf(?:inity)?|nan))';

    /**
     * @var string
     */
    private const COORDINATE_LIST_PATTERN = self::COORDINATE_PATTERN.'(?:\s*,\s*'.self::COORDINATE_PATTERN.')*';

    /**
     * @var string
     */
    private const CORNER_PATTERN = '\(\s*('.self::COORDINATE_LIST_PATTERN.')\s*\)';

    /**
     * @var string
     */
    private const BOX_REGEX = '/^'.self::CORNER_PATTERN.'\s*,\s*'.self::CORNER_PATTERN.'$/';

    /**
     * @var string
     */
    private const POINT_REGEX = '/^'.self::CORNER_PATTERN.'$/';

    /**
     * @var string
     */
    private const BARE_POINT_REGEX = '/^('.self::COORDINATE_LIST_PATTERN.')$/';

    /**
     * @var list<float>
     */
    private array $firstCorner;

    /**
     * @var list<float>|null
     */
    private ?array $secondCorner;

    /**
     * @param list<float> $firstCorner
     * @param list<float>|null $secondCorner
     *
     * @throws InvalidCubeException
     */
    public function __construct(array $firstCorner, ?array $secondCorner = null)
    {
        if ($firstCorner === []) {
            throw InvalidCubeException::forEmptyCoordinates();
        }

        if (\count($firstCorner) > self::MAX_DIMENSIONS) {
            throw InvalidCubeException::forTooManyDimensions(\count($firstCorner));
        }

        if ($secondCorner !== null && \count($secondCorner) !== \count($firstCorner)) {
            throw InvalidCubeException::forMismatchedDimensions(\count($firstCorner), \count($secondCorner));
        }

        $this->firstCorner = $firstCorner;
        // PostgreSQL renders a zero-volume box as a point, so collapse it here as well.
        // Otherwise, a stored value would never equal the one read back.
        $this->secondCorner = $secondCorner === $firstCorner ? null : $secondCorner;
    }

    public function __toString(): string
    {
        $firstCorner = $this->formatCorner($this->firstCorner);
        if ($this->secondCorner === null) {
            return $firstCorner;
        }

        return $firstCorner.','.$this->formatCorner($this->secondCorner);
    }

    /**
     * @return list<float>
     */
    public function getFirstCorner(): array
    {
        return $this->firstCorner;
    }

    /**
     * @return list<float>|null
     */
    public function getSecondCorner(): ?array
    {
        return $this->secondCorner;
    }

    public function getDimensions(): int
    {
        return \count($this->firstCorner);
    }

    public function isPoint(): bool
    {
        return $this->secondCorner === null;
    }

    /**
     * @throws InvalidCubeException
     */
    public static function point(float ...$coordinates): self
    {
        return new self(\array_values($coordinates));
    }

    /**
     * @throws InvalidCubeException
     */
    public static function fromString(string $value): self
    {
        $trimmed = \trim($value);
        // PostgreSQL accepts an optional bracketed box form, [(1,2),(3,4)], and drops the brackets on output.
        if (\str_starts_with($trimmed, '[') && \str_ends_with($trimmed, ']')) {
            $trimmed = \trim(\mb_substr($trimmed, 1, -1));
        }

        if (\preg_match(self::BOX_REGEX, $trimmed, $matches) === 1) {
            return new self(self::parseCoordinates($matches[1]), self::parseCoordinates($matches[2]));
        }

        if (\preg_match(self::POINT_REGEX, $trimmed, $matches) === 1) {
            return new self(self::parseCoordinates($matches[1]));
        }

        if (\preg_match(self::BARE_POINT_REGEX, $trimmed, $matches) === 1) {
            return new self(self::parseCoordinates($matches[1]));
        }

        throw InvalidCubeException::forInvalidFormat($value);
    }

    /**
     * @return list<float>
     */
    private static function parseCoordinates(string $coordinateList): array
    {
        $parts = \preg_split('/\s*,\s*/', \trim($coordinateList));
        \assert(\is_array($parts));

        return \array_map(self::parseCoordinate(...), $parts);
    }

    /**
     * Casting a string to float yields 0.0 for every non-finite spelling PostgreSQL uses. Those are matched explicitly.
     */
    private static function parseCoordinate(string $coordinate): float
    {
        return match (\mb_strtolower(\ltrim($coordinate, '+'))) {
            'nan', '-nan' => \NAN,
            'inf', 'infinity' => \INF,
            '-inf', '-infinity' => -\INF,
            default => (float) $coordinate,
        };
    }

    /**
     * @param list<float> $coordinates
     */
    private function formatCorner(array $coordinates): string
    {
        return '('.\implode(', ', \array_map($this->formatCoordinate(...), $coordinates)).')';
    }

    /**
     * Casting a float to string uses the `precision` ini setting, which silently rewrites stored values in full float8
     * precision. Fall back to the 17-digit form, which always round-trips, whenever the short one does not.
     */
    private function formatCoordinate(float $coordinate): string
    {
        if (\is_nan($coordinate)) {
            return 'NaN';
        }

        if (\is_infinite($coordinate)) {
            return $coordinate > 0 ? 'Infinity' : '-Infinity';
        }

        $shortForm = (string) $coordinate;

        return (float) $shortForm === $coordinate ? $shortForm : \sprintf('%.17G', $coordinate);
    }
}
