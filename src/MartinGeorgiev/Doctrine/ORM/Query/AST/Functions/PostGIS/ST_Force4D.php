<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Force4D() function.
 *
 * Forces a geometry to 4D by adding Z and M coordinates if they don't exist.
 * If Z or M coordinates exist, they are preserved.
 *
 * @see https://postgis.net/docs/ST_Force4D.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_FORCE4D(g.geometry) FROM Entity g"
 * Returns 4D geometry.
 */
class ST_Force4D extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Force4D(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
