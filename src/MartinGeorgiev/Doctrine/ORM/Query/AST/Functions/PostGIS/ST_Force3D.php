<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Force3D() function.
 *
 * Forces a geometry to 3D by adding a Z coordinate if it doesn't exist.
 * If Z coordinate exists, it is preserved.
 *
 * @see https://postgis.net/docs/ST_Force3D.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_FORCE3D(g.geometry) FROM Entity g"
 * Returns 3D geometry.
 */
class ST_Force3D extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Force3D(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
