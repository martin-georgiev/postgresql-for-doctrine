<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Split() function.
 *
 * Returns a collection of geometries created by splitting the input geometry by the blade geometry.
 * The blade must be a LineString or MultiLineString.
 *
 * @see https://postgis.net/docs/ST_Split.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_SPLIT(g.geometry, g.blade) FROM Entity g"
 * Returns geometry collection representing the split result.
 */
class ST_Split extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Split(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
