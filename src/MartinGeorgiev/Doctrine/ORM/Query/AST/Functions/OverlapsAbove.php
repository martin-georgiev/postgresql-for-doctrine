<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostGIS bounding box overlaps or is above operator (using |&>).
 *
 * Returns TRUE if A's bounding box overlaps or is above B's.
 *
 * @see https://postgis.net/docs/reference.html#Operators_Geometry
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with boolean comparison: "WHERE OVERLAPS_ABOVE(g1.geometry, g2.geometry) = TRUE"
 * Returns boolean, must be used with "= TRUE" or "= FALSE" in DQL.
 */
class OverlapsAbove extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s |&> %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
