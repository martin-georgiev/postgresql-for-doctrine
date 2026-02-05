<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Envelope() function.
 *
 * Returns the bounding box of the input geometry as a polygon.
 * The bounding box is the smallest rectangle that contains the geometry.
 *
 * @see https://postgis.net/docs/ST_Envelope.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_ENVELOPE(g.geometry) FROM Entity g"
 */
class ST_Envelope extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Envelope(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
