<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_CoordDim().
 *
 * Returns the coordinate dimension of a geometry.
 *
 * @see https://postgis.net/docs/ST_CoordDim.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_COORDDIM(g.geometry) FROM Entity g"
 */
class ST_CoordDim extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_CoordDim(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
