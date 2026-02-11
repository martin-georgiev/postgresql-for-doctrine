<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_TriangulatePolygon() function.
 *
 * Computes a constrained Delaunay triangulation of a polygon.
 * Returns a collection of triangular polygons that cover the input polygon.
 *
 * @see https://postgis.net/docs/ST_TriangulatePolygon.html
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_TRIANGULATEPOLYGON(g.geometry) FROM Entity g"
 */
class ST_TriangulatePolygon extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_TriangulatePolygon(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
