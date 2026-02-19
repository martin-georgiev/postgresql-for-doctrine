<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_Translate() function.
 *
 * Translates a geometry by the given offsets.
 * Moves the geometry in X, Y, and optionally Z directions.
 *
 * @see https://postgis.net/docs/ST_Translate.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_TRANSLATE(g.geometry, 10, 20) FROM Entity g"
 * @example Using it in DQL (3D): "SELECT ST_TRANSLATE(g.geometry, 10, 20, 5) FROM Entity g"
 */
class ST_Translate extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary,ArithmeticPrimary,ArithmeticPrimary,ArithmeticPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'ST_Translate';
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
