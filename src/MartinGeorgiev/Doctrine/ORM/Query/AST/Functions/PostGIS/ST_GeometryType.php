<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_GeometryType().
 *
 * Returns the SQL-MM type string of a geometry.
 *
 * @see https://postgis.net/docs/ST_GeometryType.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_GEOMETRYTYPE(g.geometry) FROM Entity g"
 */
class ST_GeometryType extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_GeometryType(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
