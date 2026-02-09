<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Reverse() function.
 *
 * Returns a geometry with the order of points reversed.
 * For LineString, the start and end points are swapped.
 * For Polygon, the ring orientations are reversed.
 *
 * @see https://postgis.net/docs/ST_Reverse.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_REVERSE(g.geometry) FROM Entity g"
 */
class ST_Reverse extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Reverse(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
