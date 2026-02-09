<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS n-dimensional bounding box intersects operator (using &&&).
 *
 * Returns TRUE if A's n-D bounding box intersects B's n-D bounding box.
 * This operator works with 3D and higher dimensional geometries.
 *
 * @see https://postgis.net/docs/reference.html#Operators_Geometry
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with boolean comparison: "WHERE ND_OVERLAPS(g1.geometry, g2.geometry) = TRUE"
 * Returns boolean, must be used with "= TRUE" or "= FALSE" when used in WHERE clause in DQL.
 */
class NDimensionalOverlaps extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s &&& %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
