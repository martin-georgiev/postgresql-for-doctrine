<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_NPoints().
 *
 * Returns the number of points (vertices) in a geometry.
 *
 * @see https://postgis.net/docs/ST_NPoints.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_NPOINTS(g.geometry) FROM Entity g"
 */
class ST_NPoints extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_NPoints(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
