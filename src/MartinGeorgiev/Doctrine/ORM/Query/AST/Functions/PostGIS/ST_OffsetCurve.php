<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_OffsetCurve().
 *
 * Returns an offset line at a given distance and side from an input line.
 *
 * @see https://postgis.net/docs/ST_OffsetCurve.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_OFFSETCURVE(g.geometry, 1.0) FROM Entity g"
 * @example Using it in DQL with style: "SELECT ST_OFFSETCURVE(g.geometry, 1.0, 'quad_segs=4 join=round') FROM Entity g"
 */
class ST_OffsetCurve extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,ArithmeticPrimary,StringPrimary',
            'StringPrimary,ArithmeticPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'ST_OffsetCurve';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 3;
    }
}
