<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_OrderingEquals() function.
 *
 * Tests if two geometries represent the same geometry and have points in the same directional order.
 *
 * @see https://postgis.net/docs/ST_OrderingEquals.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "WHERE ST_OrderingEquals(g.geometry1, g.geometry2) = TRUE"
 */
class ST_OrderingEquals extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_OrderingEquals(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
