<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_VoronoiPolygons().
 *
 * Returns the 2D Voronoi diagram polygons computed from the vertices of a geometry.
 *
 * @see https://postgis.net/docs/ST_VoronoiPolygons.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_VORONOIPOLYGONS(g.geometry) FROM Entity g"
 * @example Using it in DQL with tolerance: "SELECT ST_VORONOIPOLYGONS(g.geometry, 8.0) FROM Entity g"
 * @example Using it in DQL with tolerance and extend_to: "SELECT ST_VORONOIPOLYGONS(g.geometry1, 8.0, g.geometry2) FROM Entity g"
 */
class ST_VoronoiPolygons extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,ArithmeticPrimary,StringPrimary',
            'StringPrimary,ArithmeticPrimary',
            'StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'ST_VoronoiPolygons';
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }

    protected function getMaxArgumentCount(): int
    {
        return 3;
    }
}
