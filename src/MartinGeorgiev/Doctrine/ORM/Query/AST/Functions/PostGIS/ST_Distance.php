<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Distance() function.
 *
 * Returns the 2D distance between two geometries.
 * For geometry type, the units are in the units of the spatial reference system.
 *
 * @see https://postgis.net/docs/ST_Distance.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_DISTANCE(g1.geometry, g2.geometry) FROM Entity g1, Entity g2"
 * Returns numeric distance value.
 */
class ST_Distance extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Distance(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
