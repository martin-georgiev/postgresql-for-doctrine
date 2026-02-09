<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_ConvexHull() function.
 *
 * Returns the convex hull of the input geometry.
 * The convex hull is the smallest convex geometry that contains the input geometry.
 *
 * @see https://postgis.net/docs/ST_ConvexHull.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_CONVEXHULL(g.geometry) FROM Entity g"
 */
class ST_ConvexHull extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_ConvexHull(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
