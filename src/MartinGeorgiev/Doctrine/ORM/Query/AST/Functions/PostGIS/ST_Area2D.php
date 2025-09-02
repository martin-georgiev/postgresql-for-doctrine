<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Area2D() function.
 *
 * Returns the 2D area of the geometry if it is a polygon or multi-polygon.
 * For non-areal geometries, 0 is returned.
 * Ignores Z coordinates.
 *
 * @see https://postgis.net/docs/ST_Area2D.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_AREA2D(g.geometry) FROM Entity g"
 * Returns numeric 2D area value.
 */
class ST_Area2D extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Area2D(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
