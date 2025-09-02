<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_3DArea() function.
 *
 * Returns the 3D area of the geometry if it is a polygon or multi-polygon.
 * For non-areal geometries, 0 is returned.
 *
 * @see https://postgis.net/docs/ST_3DArea.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_3DAREA(g.geometry) FROM Entity g"
 * Returns numeric 3D area value.
 */
class ST_3DArea extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_3DArea(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
