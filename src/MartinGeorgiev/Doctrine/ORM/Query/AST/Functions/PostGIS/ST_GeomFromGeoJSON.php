<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_GeomFromGeoJSON() function.
 *
 * Creates a geometry from a GeoJSON representation.
 *
 * @see https://postgis.net/docs/ST_GeomFromGeoJSON.html
 * @since 4.2
 *
 * @author Jan Klan <jan@klan.com.au>
 *
 * @example Using it in DQL: "SELECT ST_GEOMFROMGEOJSON('{"type":"Point","coordinates":[0,0]}') FROM Entity g"
 */
class ST_GeomFromGeoJSON extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_GeomFromGeoJSON(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
