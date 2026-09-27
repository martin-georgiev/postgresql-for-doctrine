<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_CollectionExtract().
 *
 * Extracts a specific type from a geometry collection.
 * Returns a collection containing only geometries of the specified type.
 *
 * @see https://postgis.net/docs/ST_CollectionExtract.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_COLLECTIONEXTRACT(g.geometry) FROM Entity g"
 * @example Using it in DQL with a type: "SELECT ST_COLLECTIONEXTRACT(g.geometry, 1) FROM Entity g"
 */
class ST_CollectionExtract extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,SimpleArithmeticExpression',
            'StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'ST_CollectionExtract';
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
