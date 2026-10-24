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
    private const POINT_CAPTURE_PATTERN = '\(\s*('.PostgresFloat::PATTERN.')\s*,\s*('.PostgresFloat::PATTERN.')\s*\)';

    /**
     * @var string
     */
    protected const POINT_CAPTURE_REGEX = '/'.self::POINT_CAPTURE_PATTERN.'/';

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

    /**
     * Reads one point per match. A single pattern repeating over every point runs out of PCRE's JIT stack before 2,000
     * points, and out of its backtrack limit after some tens of thousands even when possessive.
     *
     * @return list<Point>|null null when $value is not a comma-separated list of points between the two brackets
     */
    protected static function parsePointList(string $value, string $openingBracket, string $closingBracket): ?array
    {
        $points = [];
        $offset = 0;
        $leadingPattern = \preg_quote($openingBracket, '/').'\s*';
        while (\preg_match('/'.$leadingPattern.self::POINT_CAPTURE_PATTERN.'/A', $value, $match, 0, $offset) === 1) {
            $points[] = new Point(PostgresFloat::parse($match[1]), PostgresFloat::parse($match[2]));
            $offset += \strlen($match[0]);
            $leadingPattern = '\s*,\s*';
        }

        $isClosedAfterTheLastPoint = \preg_match('/\s*'.\preg_quote($closingBracket, '/').'$/A', $value, $match, 0, $offset) === 1;

        return $points !== [] && $isClosedAfterTheLastPoint ? $points : null;
    }
}
