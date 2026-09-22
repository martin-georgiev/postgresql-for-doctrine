<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_NumInteriorRings().
 *
 * Returns the number of interior rings (holes) of a Polygon.
 * Returns null for any other geometry type.
 *
 * @see https://postgis.net/docs/ST_NumInteriorRings.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_NUMINTERIORRINGS(g.geometry1) FROM Entity g"
 */
class ST_NumInteriorRings extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_NumInteriorRings(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
