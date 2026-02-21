<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_Scale() function.
 *
 * Scales a geometry by the given factors.
 * Useful for resizing geometries while maintaining proportions.
 *
 * @see https://postgis.net/docs/ST_Scale.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_SCALE(g.geometry, 2, 2) FROM Entity g"
 * @example Using it in DQL (3D): "SELECT ST_SCALE(g.geometry, 2, 2, 1) FROM Entity g"
 */
class ST_Scale extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary,ArithmeticPrimary,ArithmeticPrimary,ArithmeticPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'ST_Scale';
    }

    protected function getMinArgumentCount(): int
    {
        return 3;
    }

    protected function getMaxArgumentCount(): int
    {
        return 4;
    }
}
