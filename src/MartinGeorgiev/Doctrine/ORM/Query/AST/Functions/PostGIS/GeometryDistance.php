<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS 2D distance between geometries operator (using <->).
 *
 * Returns the 2D distance between A and B geometries.
 * This differs from BoundingBoxDistance (using <#>), which measures distance between
 * bounding boxes only. The <-> operator is commonly used for KNN nearest-neighbor
 * ordering, and on PostgreSQL 9.5+ with PostGIS 2.2+ it returns true geometry distance;
 * on older stacks it behaved as a centroid-of-bounding-box distance approximation.
 *
 * @see https://postgis.net/docs/reference.html#Operators
 * @see https://postgis.net/docs/geometry_distance_knn.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT GEOMETRY_DISTANCE(g1.geometry, g2.geometry) FROM Entity g1, Entity g2"
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
