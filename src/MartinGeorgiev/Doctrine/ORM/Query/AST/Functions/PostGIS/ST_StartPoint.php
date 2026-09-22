<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_StartPoint().
 *
 * Returns the first point of a geometry.
 * Since PostGIS 3.2 this is no longer restricted to LineStrings, unlike its ST_EndPoint() counterpart.
 *
 * @see https://postgis.net/docs/ST_StartPoint.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_STARTPOINT(g.geometry1) FROM Entity g"
 */
class ST_StartPoint extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_StartPoint(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
