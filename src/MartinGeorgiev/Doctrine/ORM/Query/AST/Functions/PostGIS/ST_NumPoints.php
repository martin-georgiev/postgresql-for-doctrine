<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_NumPoints().
 *
 * Returns the number of points in a LineString or CircularString, and null for any other geometry type.
 * ST_NPoints() is the different, broader function that counts the vertices of an arbitrary geometry.
 *
 * @see https://postgis.net/docs/ST_NumPoints.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_NUMPOINTS(g.geometry1) FROM Entity g"
 */
class ST_NumPoints extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_NumPoints(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
