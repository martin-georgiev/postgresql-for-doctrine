<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_MaxDistance() function.
 *
 * Returns the maximum distance between two geometries.
 * This is the maximum distance between any two points, one from each geometry.
 *
 * @see https://postgis.net/docs/ST_MaxDistance.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_MAXDISTANCE(g1.geometry, g2.geometry) FROM Entity g1, Entity g2"
 */
class ST_MaxDistance extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_MaxDistance(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
