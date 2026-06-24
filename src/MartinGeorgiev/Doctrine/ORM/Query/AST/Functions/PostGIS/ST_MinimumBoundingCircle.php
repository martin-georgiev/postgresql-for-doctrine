<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_MinimumBoundingCircle().
 *
 * Returns the smallest circle polygon that contains a geometry.
 *
 * @see https://postgis.net/docs/ST_MinimumBoundingCircle.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_MINIMUMBOUNDINGCIRCLE(g.geometry) FROM Entity g"
 * @example Using it in DQL with num_segs_per_qt_circ: "SELECT ST_MINIMUMBOUNDINGCIRCLE(g.geometry, 48) FROM Entity g"
 */
class ST_MinimumBoundingCircle extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,ArithmeticPrimary',
            'StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'ST_MinimumBoundingCircle';
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
