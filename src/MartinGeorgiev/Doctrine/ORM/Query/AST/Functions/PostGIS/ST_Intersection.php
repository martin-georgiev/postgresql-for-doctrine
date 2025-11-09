<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Intersection() function.
 *
 * Returns a geometry that represents the point set intersection of two geometries.
 * The result may be a heterogeneous geometry collection.
 *
 * @see https://postgis.net/docs/ST_Intersection.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_INTERSECTION(g1.geometry, g2.geometry) FROM Entity g1, Entity g2"
 * Returns geometry representing the intersection.
 */
class ST_Intersection extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Intersection(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
