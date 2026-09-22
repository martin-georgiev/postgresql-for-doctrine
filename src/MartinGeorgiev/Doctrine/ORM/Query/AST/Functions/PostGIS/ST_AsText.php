<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_AsText().
 *
 * Returns the geometry or geography as OGC Well-Known Text, without the SRID.
 * The optional second argument caps the number of decimal digits in the coordinates.
 *
 * @see https://postgis.net/docs/ST_AsText.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_ASTEXT(g.geometry) FROM Entity g"
 * @example Using it in DQL with maximum decimal digits: "SELECT ST_ASTEXT(g.geometry, 2) FROM Entity g"
 */
class ST_AsText extends BaseVariadicFunction
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
        return 'ST_AsText';
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
