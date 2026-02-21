<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_Buffer() function.
 *
 * Returns a geometry that represents all points whose distance from the input geometry
 * is less than or equal to the distance parameter.
 *
 * @see https://postgis.net/docs/ST_Buffer.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_BUFFER(g.geometry, 10) FROM Entity g"
 * @example Using it in DQL with quad_segs: "SELECT ST_BUFFER(g.geometry, 10, 32) FROM Entity g"
 * @example Using it in DQL with buffer style: "SELECT ST_BUFFER(g.geometry, 10, 'quad_segs=8') FROM Entity g"
 */
class ST_Buffer extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,ArithmeticPrimary,ArithmeticPrimary',
            'StringPrimary,ArithmeticPrimary,StringPrimary',
            'StringPrimary,ArithmeticPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'ST_Buffer';
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
