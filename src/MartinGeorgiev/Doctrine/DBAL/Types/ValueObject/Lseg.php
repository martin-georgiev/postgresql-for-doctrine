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
    private const COORDINATE_PATTERN = '-?\d+(?:\.\d{1,6})?';

    private const POINT_PATTERN = '\(\s*'.self::COORDINATE_PATTERN.'\s*,\s*'.self::COORDINATE_PATTERN.'\s*\)';

    private const BRACKETED_LSEG_REGEX = '/^\[\s*'.self::POINT_PATTERN.'\s*,\s*'.self::POINT_PATTERN.'\s*\]$/';

    private const UNBRACKETED_LSEG_REGEX = '/^'.self::POINT_PATTERN.'\s*,\s*'.self::POINT_PATTERN.'$/';

    private const POINT_CAPTURE_REGEX = '/\(\s*('.self::COORDINATE_PATTERN.')\s*,\s*('.self::COORDINATE_PATTERN.')\s*\)/';

    public function __construct(
        private Point $start,
        private Point $end,
    ) {}

    public function __toString(): string
    {
        return \sprintf(
            '[(%s,%s),(%s,%s)]',
            $this->start->getX(),
            $this->start->getY(),
            $this->end->getX(),
            $this->end->getY()
        );
    }

    public function getStart(): Point
    {
        return $this->start;
    }

    public function getEnd(): Point
    {
        return $this->end;
    }

    public static function fromString(string $value): self
    {
        if (!\preg_match(self::BRACKETED_LSEG_REGEX, $value) && !\preg_match(self::UNBRACKETED_LSEG_REGEX, $value)) {
            throw InvalidLsegException::forInvalidFormat($value, self::BRACKETED_LSEG_REGEX);
        }

        \preg_match_all(self::POINT_CAPTURE_REGEX, $value, $matches, PREG_SET_ORDER);

        return new self(
            new Point((float) $matches[0][1], (float) $matches[0][2]),
            new Point((float) $matches[1][1], (float) $matches[1][2])
        );
    }
}
