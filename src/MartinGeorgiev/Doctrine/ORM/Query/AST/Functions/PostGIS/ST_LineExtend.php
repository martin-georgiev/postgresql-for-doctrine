<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_LineExtend() function.
 *
 * Returns a line extended forwards and backwards by specified distances.
 *
 * @see https://postgis.net/docs/ST_LineExtend.html
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_LineExtend(g.geometry, 5, 6) FROM Entity g"
 */
class ST_LineExtend extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary,ArithmeticPrimary,ArithmeticPrimary',
            'StringPrimary,ArithmeticPrimary',
            'StringPrimary,',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'ST_LineExtend';
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
