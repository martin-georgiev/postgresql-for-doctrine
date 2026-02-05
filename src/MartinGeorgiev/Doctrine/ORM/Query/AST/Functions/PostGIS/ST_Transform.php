<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Transform() function.
 *
 * Returns a new geometry with coordinates transformed to the specified SRID.
 * Useful for converting between different coordinate reference systems.
 *
 * @see https://postgis.net/docs/ST_Transform.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_TRANSFORM(g.geometry, 4326) FROM Entity g"
 */
class ST_Transform extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Transform(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('Literal');
    }
}
