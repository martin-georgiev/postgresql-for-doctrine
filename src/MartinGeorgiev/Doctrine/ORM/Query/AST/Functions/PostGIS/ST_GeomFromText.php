<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_GeomFromText().
 *
 * Creates a geometry from its OGC Well-Known Text representation.
 * Without the optional SRID argument the geometry carries no spatial reference system.
 *
 * @see https://postgis.net/docs/ST_GeomFromText.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_GEOMFROMTEXT('POINT(1 2)') FROM Entity g"
 * @example Using it in DQL with SRID: "SELECT ST_GEOMFROMTEXT('POINT(1 2)', 4326) FROM Entity g"
 */
class ST_GeomFromText extends BaseVariadicFunction
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
        return 'ST_GeomFromText';
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
