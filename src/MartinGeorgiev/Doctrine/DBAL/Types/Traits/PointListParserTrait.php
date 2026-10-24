<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point;
use MartinGeorgiev\Utils\PostgresFloat;

/**
 * @internal
 *
 * @since 4.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
trait PointListParserTrait
{
    /**
     * One match per point, because a single pattern repeating over every point exhausts PCRE's limits on long values.
     *
     * @return list<Point>|null
     */
    private static function parsePointList(string $value, string $openingBracket, string $closingBracket): ?array
    {
        $points = [];
        $offset = 0;
        $separatorPattern = \preg_quote($openingBracket, '/').'\s*';
        while (\preg_match('/'.$separatorPattern.Point::PATTERN.'/A', $value, $match, 0, $offset) === 1) {
            $points[] = new Point(PostgresFloat::parse($match[1]), PostgresFloat::parse($match[2]));
            $offset += \strlen($match[0]);
            $separatorPattern = '\s*,\s*';
        }

        $isClosedAfterTheLastPoint = \preg_match('/\s*'.\preg_quote($closingBracket, '/').'$/A', $value, $match, 0, $offset) === 1;

        return $points !== [] && $isClosedAfterTheLastPoint ? $points : null;
    }
}
