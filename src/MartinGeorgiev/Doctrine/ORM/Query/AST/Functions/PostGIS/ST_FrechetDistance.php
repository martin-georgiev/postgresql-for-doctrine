<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_FrechetDistance() function.
 *
 * Returns the Fréchet distance between two geometries.
 * This is a measure of similarity between curves that takes into account the location and ordering of points.
 *
 * @see https://postgis.net/docs/ST_FrechetDistance.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_FRECHETDISTANCE(g1.geometry, g2.geometry) FROM Entity g1, Entity g2"
 */
class ST_FrechetDistance extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_FrechetDistance(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
