<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_3DDistance() function.
 *
 * Returns the 3D distance between two geometries.
 * For geometry type, the units are in the units of the spatial reference system.
 *
 * @see https://postgis.net/docs/ST_3DDistance.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_3DDISTANCE(g1.geometry, g2.geometry) FROM Entity g1, Entity g2"
 * Returns numeric 3D distance value.
 */
class ST_3DDistance extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_3DDistance(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
