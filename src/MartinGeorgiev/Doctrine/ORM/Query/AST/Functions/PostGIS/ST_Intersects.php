<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Intersects() function.
 *
 * Tests if two geometries intersect (they have at least one point in common).
 *
 * @see https://postgis.net/docs/ST_Intersects.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "WHERE ST_Intersects(g.geometry1, g.geometry2) = TRUE"
 * Returns boolean, must be used with "= TRUE" or "= FALSE" in DQL.
 */
class ST_Intersects extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Intersects(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
