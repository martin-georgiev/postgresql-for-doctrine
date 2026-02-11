<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_SymDifference() function.
 *
 * Returns a geometry that represents the point set symmetric difference of two geometries.
 * The result represents the points in A that are not in B, plus the points in B that are not in A.
 *
 * @see https://postgis.net/docs/ST_SymDifference.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_SYMDIFFERENCE(g1.geometry, g2.geometry) FROM Entity g1, Entity g2"
 */
class ST_SymDifference extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_SymDifference(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
