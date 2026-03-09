<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunctionWithOptionalBooleanLastArgument;

/**
 * Implementation of PostGIS ST_Area() function.
 *
 * Returns the area of the geometry if it is a polygon or multi-polygon.
 * For non-areal geometries, 0 is returned.
 *
 * @see https://postgis.net/docs/ST_Area.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL (geometry): "SELECT ST_AREA(g.geometry) FROM Entity g"
 * @example Using it in DQL (geography): "SELECT ST_AREA(g.geography, TRUE) FROM Entity g"
 */
class ST_Area extends BaseVariadicFunctionWithOptionalBooleanLastArgument
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'ST_Area';
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }

    protected function getMaxArgumentCount(): int
    {
        return 2;
    }
}
