<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_FrechetDistance() function.
 *
 * Returns the Fréchet distance between two geometries.
 * This is a measure of similarity between curves that takes into account the location and ordering of points.
 *
 * @see https://postgis.net/docs/ST_FrechetDistance.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_FRECHETDISTANCE(g1.geometry, g2.geometry) FROM Entity g1, Entity g2"
 * @example Using it in DQL with densify: "SELECT ST_FRECHETDISTANCE(g1.geometry, g2.geometry, 0.5) FROM Entity g1, Entity g2"
 */
class ST_FrechetDistance extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary,StringPrimary,ArithmeticPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'ST_FrechetDistance';
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
