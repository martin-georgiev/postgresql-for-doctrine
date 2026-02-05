<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Subdivide() function.
 *
 * Returns a set of geometries where no geometry has more than the specified number of vertices.
 * Useful for breaking down complex geometries into simpler parts.
 *
 * @see https://postgis.net/docs/ST_Subdivide.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_SUBDIVIDE(g.geometry, 256) FROM Entity g"
 */
class ST_Subdivide extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Subdivide(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('Literal');
    }
}
