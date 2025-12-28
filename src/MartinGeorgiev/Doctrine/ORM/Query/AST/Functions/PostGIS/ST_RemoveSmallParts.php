<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_RemoveSmallParts() function.
 *
 * Removes small polygon rings and linestrings from a geometry.
 * Useful for cleaning up geometries with insignificant parts.
 *
 * @see https://postgis.net/docs/ST_RemoveSmallParts.html
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_REMOVESMALLPARTS(g.geometry, 100, 10) FROM Entity g"
 * Returns geometry with small parts removed.
 */
class ST_RemoveSmallParts extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_RemoveSmallParts(%s, %s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
    }
}
