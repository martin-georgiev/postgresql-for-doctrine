<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_Subdivide() function.
 *
 * Returns a set of geometries where no geometry has more than the specified number of vertices.
 * Useful for breaking down complex geometries into simpler parts.
 *
 * @see https://postgis.net/docs/ST_Subdivide.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_SUBDIVIDE(g.geometry, 256) FROM Entity g"
 * @example Using it in DQL with gridSize: "SELECT ST_SUBDIVIDE(g.geometry, 256, 0.5) FROM Entity g"
 */
class ST_Subdivide extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary,ArithmeticPrimary,ArithmeticPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'ST_Subdivide';
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
