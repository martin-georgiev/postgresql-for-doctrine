<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_PointOnSurface().
 *
 * Returns a point guaranteed to lie on the surface of a geometry.
 *
 * @see https://postgis.net/docs/ST_PointOnSurface.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_POINTONSURFACE(g.geometry) FROM Entity g"
 */
class ST_PointOnSurface extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_PointOnSurface(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
