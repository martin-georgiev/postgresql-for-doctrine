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
