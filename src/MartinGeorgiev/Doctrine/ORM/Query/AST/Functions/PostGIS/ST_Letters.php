<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_Letters() function.
 *
 * Creates geometries that look like letters.
 * Useful for labeling and text rendering in spatial applications.
 *
 * @see https://postgis.net/docs/ST_Letters.html
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_LETTERS('PostGIS') FROM Entity g"
 * @example Using it in DQL: "SELECT ST_LETTERS('PostGIS', :font) FROM Entity g"
 * Returns a geometry collection of letter shapes.
 */
class ST_Letters extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'ST_Letters';
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
