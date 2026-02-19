<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_HausdorffDistance() function.
 *
 * Returns the Hausdorff distance between two geometries.
 * This is a measure of how similar two geometries are.
 *
 * @see https://postgis.net/docs/ST_HausdorffDistance.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_HAUSDORFFDISTANCE(g1.geometry, g2.geometry) FROM Entity g1, Entity g2"
 * @example Using it in DQL with densify: "SELECT ST_HAUSDORFFDISTANCE(g1.geometry, g2.geometry, 0.5) FROM Entity g1, Entity g2"
 */
class ST_HausdorffDistance extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary,StringPrimary,ArithmeticPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'ST_HausdorffDistance';
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
