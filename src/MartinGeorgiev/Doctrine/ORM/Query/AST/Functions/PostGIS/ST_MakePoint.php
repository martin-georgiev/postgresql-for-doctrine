<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_MakePoint().
 *
 * Creates a 2D, 3DZ, or 4D point geometry from coordinate values.
 *
 * @see https://postgis.net/docs/ST_MakePoint.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_MAKEPOINT(1.0, 2.0) FROM Entity g"
 * @example Using it in DQL: "SELECT ST_MAKEPOINT(1.0, 2.0, 3.0) FROM Entity g"
 * @example Using it in DQL: "SELECT ST_MAKEPOINT(1.0, 2.0, 3.0, 4.0) FROM Entity g"
 */
class ST_MakePoint extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['ArithmeticPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'ST_MakePoint';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 4;
    }
}
