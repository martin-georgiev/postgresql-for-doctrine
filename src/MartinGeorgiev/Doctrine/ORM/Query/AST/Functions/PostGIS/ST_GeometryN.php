<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_GeometryN().
 *
 * Returns the Nth element of a geometry collection or a multi-geometry.
 * Index is 1-based. For an atomic geometry the only valid index is 1.
 *
 * @see https://postgis.net/docs/ST_GeometryN.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_GEOMETRYN(g.geometry1, 1) FROM Entity g"
 */
class ST_GeometryN extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_GeometryN(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
    }
}
