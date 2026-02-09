<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_UnaryUnion() function.
 *
 * Performs unary union on a geometry.
 * Useful for dissolving internal boundaries in polygons.
 *
 * @see https://postgis.net/docs/ST_UnaryUnion.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_UNARYUNION(g.geometry) FROM Entity g"
 */
class ST_UnaryUnion extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_UnaryUnion(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
