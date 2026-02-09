<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Boundary() function.
 *
 * Returns the boundary of a geometry.
 * For point geometries, the boundary is empty.
 * For line geometries, the boundary consists of the endpoints.
 * For polygon geometries, the boundary is the outer ring.
 *
 * @see https://postgis.net/docs/ST_Boundary.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_BOUNDARY(g.geometry) FROM Entity g"
 */
class ST_Boundary extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Boundary(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
