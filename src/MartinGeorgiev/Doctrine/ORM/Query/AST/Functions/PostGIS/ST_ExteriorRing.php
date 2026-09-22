<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_ExteriorRing().
 *
 * Returns a LineString representing the exterior ring of a Polygon.
 * Returns null for any other geometry type.
 *
 * @see https://postgis.net/docs/ST_ExteriorRing.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_EXTERIORRING(g.geometry1) FROM Entity g"
 */
class ST_ExteriorRing extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_ExteriorRing(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
