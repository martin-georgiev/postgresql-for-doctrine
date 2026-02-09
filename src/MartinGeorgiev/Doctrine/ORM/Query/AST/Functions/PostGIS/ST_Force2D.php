<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Force2D() function.
 *
 * Forces the geometry into 2D mode by removing any Z or M coordinates.
 * Useful for ensuring compatibility with 2D operations.
 *
 * @see https://postgis.net/docs/ST_Force2D.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_FORCE2D(g.geometry) FROM Entity g"
 */
class ST_Force2D extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Force2D(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
