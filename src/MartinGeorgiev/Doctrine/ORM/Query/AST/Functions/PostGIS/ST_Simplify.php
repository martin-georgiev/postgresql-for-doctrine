<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Simplify() function.
 *
 * Returns a simplified version of the input geometry using the Douglas-Peucker algorithm.
 * The tolerance parameter controls the degree of simplification.
 *
 * @see https://postgis.net/docs/ST_Simplify.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_SIMPLIFY(g.geometry, 0.5) FROM Entity g"
 */
class ST_Simplify extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Simplify(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
    }
}
