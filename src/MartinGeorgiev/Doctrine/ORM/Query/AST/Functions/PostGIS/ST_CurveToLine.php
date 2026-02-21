<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_CurveToLine() function.
 *
 * Converts curved geometries to linear geometries.
 *
 * @see https://postgis.net/docs/ST_CurveToLine.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_CURVETOLINE(g.geometry) FROM Entity g"
 * @example Using it in DQL with options: "SELECT ST_CURVETOLINE(g.geometry, 0.01, 1, 0) FROM Entity g"
 */
class ST_CurveToLine extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,ArithmeticPrimary,ArithmeticPrimary,ArithmeticPrimary',
            'StringPrimary,ArithmeticPrimary,ArithmeticPrimary',
            'StringPrimary,ArithmeticPrimary',
            'StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'ST_CurveToLine';
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }

    protected function getMaxArgumentCount(): int
    {
        return 4;
    }
}
