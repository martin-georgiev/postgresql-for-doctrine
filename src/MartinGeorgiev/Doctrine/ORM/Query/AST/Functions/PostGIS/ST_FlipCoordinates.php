<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_FlipCoordinates().
 *
 * Returns a version of the geometry with X and Y axis flipped.
 *
 * @see https://postgis.net/docs/ST_FlipCoordinates.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_FLIPCOORDINATES(g.geometry) FROM Entity g"
 */
class ST_FlipCoordinates extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_FlipCoordinates(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
