<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunctionWithOptionalBooleanLastArgument;

/**
 * Implementation of PostGIS ST_LineInterpolatePoint().
 *
 * Returns a point interpolated along a line at a fractional position.
 * For geography types, an optional boolean parameter controls whether to use spheroid calculations.
 *
 * @see https://postgis.net/docs/ST_LineInterpolatePoint.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_LINEINTERPOLATEPOINT(g.geometry, 0.5) FROM Entity g"
 * @example Using it in DQL (geography): "SELECT ST_LINEINTERPOLATEPOINT(g.geography, 0.5, 'true') FROM Entity g"
 */
class ST_LineInterpolatePoint extends BaseVariadicFunctionWithOptionalBooleanLastArgument
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
        return 'ST_LineInterpolatePoint';
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
