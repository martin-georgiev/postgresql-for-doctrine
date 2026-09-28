<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Utils\PostgresFloat;

/**
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract readonly class BaseGeometricValue implements \Stringable
{
    /**
     * @var string
     */
    protected const POINT_PATTERN = '\(\s*'.PostgresFloat::PATTERN.'\s*,\s*'.PostgresFloat::PATTERN.'\s*\)';

    /**
     * @var string
     */
    protected const POINT_CAPTURE_REGEX = '/\(\s*('.PostgresFloat::PATTERN.')\s*,\s*('.PostgresFloat::PATTERN.')\s*\)/';

    /**
     * @return list<Point>
     */
    protected static function extractPoints(string $value): array
    {
        \preg_match_all(self::POINT_CAPTURE_REGEX, $value, $matches, PREG_SET_ORDER);

        return \array_map(
            static fn (array $match): Point => new Point(PostgresFloat::parse($match[1]), PostgresFloat::parse($match[2])),
            $matches
        );
    }
}
