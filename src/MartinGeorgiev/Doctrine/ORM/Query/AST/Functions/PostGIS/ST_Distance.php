<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunctionWithOptionalBoolean;

/**
 * Implementation of PostGIS ST_Distance() function.
 *
 * Returns the 2D distance between two geometries.
 * For geometry type, the units are in the units of the spatial reference system.
 *
 * @see https://postgis.net/docs/ST_Distance.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_DISTANCE(g1.geometry, g2.geometry) FROM Entity g1, Entity g2"
 * @example Using it in DQL (geography): "SELECT ST_DISTANCE(g1.geography, g2.geography, TRUE) FROM Entity g1, Entity g2"
 */
class ST_Distance extends BaseVariadicFunctionWithOptionalBoolean
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'ST_Distance';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 3;
    }
}
