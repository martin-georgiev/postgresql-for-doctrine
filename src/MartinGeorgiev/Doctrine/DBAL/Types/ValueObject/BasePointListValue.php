<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Utils\PostgresFloat;

/**
 * @internal
 *
 * @since 4.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract readonly class BasePointListValue extends BaseGeometricValue
{
    /**
     * @var string
     */
    private const POINT_PATTERN = '\(\s*('.PostgresFloat::PATTERN.')\s*,\s*('.PostgresFloat::PATTERN.')\s*\)';

    /**
     * One match per point, because a single pattern repeating over every point exhausts PCRE's limits on long values.
     *
     * @return list<Point>|null
     */
    protected static function parsePointList(string $value, string $openingBracket = '', string $closingBracket = '', ?int $exactPointCount = null): ?array
    {
        $points = [];
        $offset = 0;
        $leadingPattern = $openingBracket === '' ? '' : \preg_quote($openingBracket, '/').'\s*';
        while (\preg_match('/'.$leadingPattern.self::POINT_PATTERN.'/A', $value, $match, 0, $offset) === 1) {
            if (\count($points) === $exactPointCount) {
                return null;
            }

            $points[] = new Point(PostgresFloat::parse($match[1]), PostgresFloat::parse($match[2]));
            $offset += \strlen($match[0]);
            $leadingPattern = '\s*,\s*';
        }

        $closingPattern = $closingBracket === '' ? '' : '\s*'.\preg_quote($closingBracket, '/');
        $isClosedAfterTheLastPoint = \preg_match('/'.$closingPattern.'$/A', $value, $match, 0, $offset) === 1;
        $hasTheExpectedPointCount = $exactPointCount === null ? $points !== [] : \count($points) === $exactPointCount;

        return $isClosedAfterTheLastPoint && $hasTheExpectedPointCount ? $points : null;
    }
}
