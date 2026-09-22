<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_GeomFromEWKT().
 *
 * Creates a geometry from its Extended Well-Known Text representation, taking the SRID from the prefix.
 *
 * @see https://postgis.net/docs/ST_GeomFromEWKT.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_GEOMFROMEWKT('SRID=4326;POINT(1 2)') FROM Entity g"
 */
class ST_GeomFromEWKT extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_GeomFromEWKT(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
