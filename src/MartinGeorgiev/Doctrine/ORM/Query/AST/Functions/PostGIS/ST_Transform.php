<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_Transform() function.
 *
 * Returns a new geometry with coordinates transformed to the specified SRID.
 * Useful for converting between different coordinate reference systems.
 *
 * @see https://postgis.net/docs/ST_Transform.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_TRANSFORM(g.geometry, 4326) FROM Entity g"
 * @example Using it in DQL (PROJ): "SELECT ST_TRANSFORM(g.geometry, '+proj=longlat') FROM Entity g"
 * @example Using it in DQL (from/to): "SELECT ST_TRANSFORM(g.geometry, '+proj=utm', '+proj=longlat') FROM Entity g"
 */
class ST_Transform extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,StringPrimary,StringPrimary',
            'StringPrimary,StringPrimary,ArithmeticPrimary',
            'StringPrimary,ArithmeticPrimary',
            'StringPrimary,StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'ST_Transform';
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
