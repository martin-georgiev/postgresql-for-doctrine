<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostGIS 2D distance between geometries operator (using <->).
 *
 * Returns the 2D distance between A and B geometries.
 * This is different from the existing Distance class which uses <@> for point distance.
 *
 * @see https://postgis.net/docs/reference.html#Operators_Distance
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT GEOMETRY_DISTANCE(g1.geometry, g2.geometry) FROM Entity g1, Entity g2"
 * Returns numeric distance value.
 */
class GeometryDistance extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s <-> %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
