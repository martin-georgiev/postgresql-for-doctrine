<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunctionWithOptionalBooleanLastArgument;

/**
 * Implementation of PostGIS ST_LineLocatePoint().
 *
 * Returns the fractional location of the closest point on a line to a given point.
 *
 * @see https://postgis.net/docs/ST_LineLocatePoint.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_LINELOCATEPOINT(g.geometry1, g.geometry2) FROM Entity g"
 * @example Using it in DQL with use_spheroid: "SELECT ST_LINELOCATEPOINT(g.geography1, g.geography2, 'true') FROM Entity g"
 */
class ST_LineLocatePoint extends BaseVariadicFunctionWithOptionalBooleanLastArgument
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'ST_LineLocatePoint';
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
