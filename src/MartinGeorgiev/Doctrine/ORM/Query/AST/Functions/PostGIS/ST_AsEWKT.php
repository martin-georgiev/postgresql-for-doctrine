<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_AsEWKT().
 *
 * Returns the geometry or geography as Extended Well-Known Text, prefixed with the SRID when one is set.
 * The optional second argument caps the number of decimal digits in the coordinates.
 *
 * @see https://postgis.net/docs/ST_AsEWKT.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_ASEWKT(g.geometry) FROM Entity g"
 * @example Using it in DQL with maximum decimal digits: "SELECT ST_ASEWKT(g.geometry, 2) FROM Entity g"
 */
class ST_AsEWKT extends BaseVariadicFunction
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
        return 'ST_AsEWKT';
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
