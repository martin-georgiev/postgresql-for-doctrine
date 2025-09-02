<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Length2D() function.
 *
 * Returns the 2D length of a LineString/MultiLineString or 2D perimeter of areal geometries.
 * Ignores Z coordinates.
 *
 * @see https://postgis.net/docs/ST_Length2D.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_LENGTH2D(g.geometry) FROM Entity g"
 * Returns 2D length as numeric value.
 */
class ST_Length2D extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Length2D(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
