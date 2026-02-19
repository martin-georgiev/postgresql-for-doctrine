<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_Rotate() function.
 *
 * Rotates a geometry by the given angle around the origin.
 * Angle is in radians, positive values rotate counterclockwise.
 *
 * @see https://postgis.net/docs/ST_Rotate.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_ROTATE(g.geometry, 1.5708) FROM Entity g"
 * @example Using it in DQL with origin: "SELECT ST_ROTATE(g.geometry, 1.5708, 0, 0) FROM Entity g"
 * @example Using it in DQL with point origin: "SELECT ST_ROTATE(g.geometry, 1.5708, g.centroid) FROM Entity g"
 */
class ST_Rotate extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,ArithmeticPrimary,ArithmeticPrimary,ArithmeticPrimary',
            'StringPrimary,ArithmeticPrimary,StringPrimary',
            'StringPrimary,ArithmeticPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'ST_Rotate';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 4;
    }
}
