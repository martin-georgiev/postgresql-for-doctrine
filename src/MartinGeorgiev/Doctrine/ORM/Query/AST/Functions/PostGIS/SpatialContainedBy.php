<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS spatial bounding box contained by operator (using @).
 *
 * Returns TRUE if A's bounding box is contained by B's.
 * This is the spatial version of the @ operator, distinct from the array/JSON version.
 *
 * @see https://postgis.net/docs/reference.html#Operators_Geometry
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with boolean comparison: "WHERE SPATIAL_CONTAINED_BY(g1.geometry, g2.geometry) = TRUE"
 * Returns boolean, must be used with "= TRUE" or "= FALSE" in DQL.
 */
class SpatialContainedBy extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s @ %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
