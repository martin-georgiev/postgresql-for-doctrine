<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_GeneratePoints().
 *
 * Generates a multipoint of random points inside a polygon geometry.
 *
 * @see https://postgis.net/docs/ST_GeneratePoints.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_GENERATEPOINTS(g.geometry, 10) FROM Entity g"
 * @example Using it in DQL with seed: "SELECT ST_GENERATEPOINTS(g.geometry, 10, 42) FROM Entity g"
 */
class ST_GeneratePoints extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,ArithmeticPrimary,ArithmeticPrimary',
            'StringPrimary,ArithmeticPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'ST_GeneratePoints';
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
