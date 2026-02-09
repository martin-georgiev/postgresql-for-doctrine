<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Area() function.
 *
 * Returns the area of the geometry if it is a polygon or multi-polygon.
 * For non-areal geometries, 0 is returned.
 *
 * @see https://postgis.net/docs/ST_Area.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_AREA(g.geometry) FROM Entity g"
 */
class ST_Area extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Area(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
