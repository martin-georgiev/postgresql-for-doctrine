<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Project() function.
 *
 * Projects a point along a geodesic.
 * Useful for calculating positions along great circle paths.
 *
 * @see https://postgis.net/docs/ST_Project.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_PROJECT(g.geometry, :distance, :azimuth) FROM Entity g"
 * Returns projected point.
 */
class ST_Project extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Project(%s, %s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('SimpleArithmeticExpression');
        $this->addNodeMapping('SimpleArithmeticExpression');
    }
}
