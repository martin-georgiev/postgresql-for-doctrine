<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_EndPoint().
 *
 * Returns the last point of a LineString or CircularString.
 * Returns null for any other geometry type.
 *
 * @see https://postgis.net/docs/ST_EndPoint.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_ENDPOINT(g.geometry1) FROM Entity g"
 */
class ST_EndPoint extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_EndPoint(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
