<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_Point().
 *
 * Creates a point with X, Y and an optional SRID.
 *
 * @see https://postgis.net/docs/ST_Point.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_POINT(1.0, 2.0) FROM Entity g"
 * @example Using it in DQL with SRID: "SELECT ST_POINT(1.0, 2.0, 4326) FROM Entity g"
 */
class ST_Point extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['ArithmeticPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'ST_Point';
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
