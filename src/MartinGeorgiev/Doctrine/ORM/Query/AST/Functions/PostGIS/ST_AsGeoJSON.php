<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_AsGeoJSON() function.
 *
 * Returns the geometry as a GeoJSON element.
 * Optional parameters control decimal precision and output options.
 *
 * @see https://postgis.net/docs/ST_AsGeoJSON.html
 * @since 4.2
 *
 * @author Jan Klan <jan@klan.com.au>
 *
 * @example Using it in DQL: "SELECT ST_ASGEOJSON(g.geometry) FROM Entity g"
 * @example Using it in DQL: "SELECT ST_ASGEOJSON(g.geometry, 6) FROM Entity g"
 * @example Using it in DQL: "SELECT ST_ASGEOJSON(g.geometry, 6, 2) FROM Entity g"
 */
class ST_AsGeoJSON extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,ArithmeticPrimary,ArithmeticPrimary',
            'StringPrimary,ArithmeticPrimary',
            'StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'ST_AsGeoJSON';
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }

    protected function getMaxArgumentCount(): int
    {
        return 3;
    }
}
