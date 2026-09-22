<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_InteriorRingN().
 *
 * Returns the Nth interior ring (hole) of a Polygon as a LineString.
 * Index is 1-based. Returns null for any other geometry type or an out-of-range index.
 *
 * @see https://postgis.net/docs/ST_InteriorRingN.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_INTERIORRINGN(g.geometry1, 1) FROM Entity g"
 */
class ST_InteriorRingN extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_InteriorRingN(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
    }
}
