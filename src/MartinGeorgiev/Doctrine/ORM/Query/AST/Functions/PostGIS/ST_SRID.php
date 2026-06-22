<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_SRID().
 *
 * Returns the spatial reference system identifier for a geometry.
 *
 * @see https://postgis.net/docs/ST_SRID.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_SRID(g.geometry) FROM Entity g"
 */
class ST_SRID extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_SRID(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
