<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Length() function.
 *
 * Returns the 2D length of the geometry if it is a LineString or MultiLineString.
 * For areal geometries, the perimeter is returned.
 *
 * @see https://postgis.net/docs/ST_Length.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_LENGTH(g.geometry) FROM Entity g"
 */
class ST_Length extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Length(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
