<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_SimplifyPreserveTopology() function.
 *
 * Simplifies geometry while preserving topology.
 * Safer than ST_Simplify as it preserves topological relationships.
 *
 * @see https://postgis.net/docs/ST_SimplifyPreserveTopology.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_SIMPLIFYPRESERVETOPOLOGY(g.geometry, 0.5) FROM Entity g"
 */
class ST_SimplifyPreserveTopology extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_SimplifyPreserveTopology(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
    }
}
