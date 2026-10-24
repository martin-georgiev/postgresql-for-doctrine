<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Utils\PostgresFloat;

/**
 * @internal
 *
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract readonly class BaseGeometricValue implements \Stringable
{
    /**
     * @var string
     */
    private const POINT_PATTERN = '\(\s*('.PostgresFloat::PATTERN.')\s*,\s*('.PostgresFloat::PATTERN.')\s*\)';

    /**
     * Reads one point per match. A single pattern repeating over every point runs out of PCRE's JIT stack before 2,000
     * points, and out of its backtrack limit after some tens of thousands even when possessive.
     *
     * Whitespace is skipped only inside brackets, so an unbracketed list must start and end with its points.
     *
     * @return list<Point>|null null when $value is not a comma-separated list of points between the two brackets
     */
    protected static function parsePointList(string $value, string $openingBracket = '', string $closingBracket = ''): ?array
    {
        $points = [];
        $offset = 0;
        $leadingPattern = $openingBracket === '' ? '' : \preg_quote($openingBracket, '/').'\s*';
        while (\preg_match('/'.$leadingPattern.self::POINT_PATTERN.'/A', $value, $match, 0, $offset) === 1) {
            $points[] = new Point(PostgresFloat::parse($match[1]), PostgresFloat::parse($match[2]));
            $offset += \strlen($match[0]);
            $leadingPattern = '\s*,\s*';
        }

        $closingPattern = $closingBracket === '' ? '' : '\s*'.\preg_quote($closingBracket, '/');
        $isClosedAfterTheLastPoint = \preg_match('/'.$closingPattern.'$/A', $value, $match, 0, $offset) === 1;

        return $points !== [] && $isClosedAfterTheLastPoint ? $points : null;
    }
}
