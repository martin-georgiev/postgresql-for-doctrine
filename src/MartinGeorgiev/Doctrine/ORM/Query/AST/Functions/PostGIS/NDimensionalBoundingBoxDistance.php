<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS n-D bounding box distance operator (using <<#>>).
 *
 * Returns the n-D distance between A and B bounding boxes.
 * This operator works with multi-dimensional geometries.
 *
 * Note: The <<#>> operator was removed from PostGIS in version 2.2.0 when true KNN distance
 * support was added to the <<->> operator, making bounding box distance variants obsolete.
 * This class is kept for legacy compatibility only and will not work with PostGIS 2.2+.
 * The recommendation is to use NDimensionalCentroidDistance (using <<->>) instead.
 *
 * @see https://postgis.net/docs/reference.html#Operators_Distance
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ND_BOUNDING_BOX_DISTANCE(g1.geometry, g2.geometry) FROM Entity g1, Entity g2"
 */
class NDimensionalBoundingBoxDistance extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s <<#>> %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
