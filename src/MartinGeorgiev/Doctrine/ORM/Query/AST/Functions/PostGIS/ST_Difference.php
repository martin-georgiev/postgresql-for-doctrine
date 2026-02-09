<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Difference() function.
 *
 * Returns a geometry that represents the point set difference of two geometries.
 * The result represents the points in A that are not in B.
 *
 * @see https://postgis.net/docs/ST_Difference.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_DIFFERENCE(g1.geometry, g2.geometry) FROM Entity g1, Entity g2"
 */
class ST_Difference extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Difference(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
