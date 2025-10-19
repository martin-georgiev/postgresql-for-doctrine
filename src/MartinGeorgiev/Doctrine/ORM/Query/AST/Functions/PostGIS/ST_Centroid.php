<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Centroid() function.
 *
 * Returns the geometric center of a geometry.
 * For point geometries, the centroid is the point itself.
 * For line geometries, the centroid is the midpoint.
 * For polygon geometries, the centroid is the center of mass.
 *
 * @see https://postgis.net/docs/ST_Centroid.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_CENTROID(g.geometry) FROM Entity g"
 * Returns point geometry representing the centroid.
 */
class ST_Centroid extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Centroid(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
