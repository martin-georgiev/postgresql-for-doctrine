<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Perimeter() function.
 *
 * Returns the 2D perimeter of the geometry if it is a polygon or multi-polygon.
 * For non-areal geometries, 0 is returned.
 *
 * @see https://postgis.net/docs/ST_Perimeter.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_PERIMETER(g.geometry) FROM Entity g"
 * Returns numeric perimeter value.
 */
class ST_Perimeter extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Perimeter(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
