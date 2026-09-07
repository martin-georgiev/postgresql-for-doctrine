<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidCubeException;

/**
 * Represents a PostgreSQL cube value, provided by the cube extension.
 *
 * A cube is either a point, written as a single coordinate group — (1, 2, 3) —
 * or a box spanned by two opposite corners — (1, 2, 3),(4, 5, 6). Both corners
 * always carry the same number of dimensions.
 *
 * PostgreSQL keeps the two corners in the order they were written and collapses
 * a zero-volume box back into a point, so this value object normalizes the same
 * way: equal corners are stored as a point and never re-emitted as a box.
 *
 * PostgreSQL also accepts NaN and Infinity coordinates. Those are rejected here,
 * because PHP cannot parse them back from the textual form PostgreSQL emits,
 * which would break the round-trip guarantee.
 *
 * @see https://www.postgresql.org/docs/18/cube.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final readonly class Cube implements \Stringable
{
    /**
     * @var string
     */
    private const COORDINATE_PATTERN = '[+-]?(?:\d+(?:\.\d*)?|\.\d+)(?:[eE][+-]?\d+)?';

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
            throw InvalidCubeException::forEmptyCoordinates(\count($firstCorner));
        }

        $this->assertFiniteCoordinates($firstCorner);

        if ($secondCorner !== null) {
            $this->assertFiniteCoordinates($secondCorner);

            if (\count($secondCorner) !== \count($firstCorner)) {
                throw InvalidCubeException::forMismatchedDimensions(
                    \sprintf('%d and %d', \count($firstCorner), \count($secondCorner))
                );
            }
        }

        $this->firstCorner = $firstCorner;
        // PostgreSQL renders a zero-volume box as a point, so collapse it here as
        // well — otherwise a stored value would never equal the one read back.
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
        // PostgreSQL accepts an optional bracketed box form, [(1,2),(3,4)], and
        // drops the brackets on output.
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
     * @param list<float> $coordinates
     *
     * @throws InvalidCubeException
     */
    private function assertFiniteCoordinates(array $coordinates): void
    {
        foreach ($coordinates as $coordinate) {
            if (!\is_finite($coordinate)) {
                throw InvalidCubeException::forNonFiniteCoordinate($coordinate);
            }
        }
    }

    /**
     * @return list<float>
     */
    private static function parseCoordinates(string $coordinateList): array
    {
        $parts = \preg_split('/\s*,\s*/', \trim($coordinateList));
        \assert(\is_array($parts));

        return \array_map(static fn (string $part): float => (float) $part, $parts);
    }

    /**
     * @param list<float> $coordinates
     */
    private function formatCorner(array $coordinates): string
    {
        return '('.\implode(', ', \array_map($this->formatCoordinate(...), $coordinates)).')';
    }

    private function formatCoordinate(float $coordinate): string
    {
        // Casting a float to string uses the `precision` ini setting (14 significant
        // digits by default), which silently rewrites values PostgreSQL stores in
        // full float8 precision. Fall back to the 17-digit form, which always
        // round-trips, whenever the short one does not.
        $shortForm = (string) $coordinate;

        return (float) $shortForm === $coordinate ? $shortForm : \sprintf('%.17G', $coordinate);
    }
}
