<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_MakeValid().
 *
 * Returns a valid geometry representing the same points as the input, repairing self-intersections and other
 * defects instead of erroring on them. The optional params argument selects the repair algorithm, e.g.
 * "method=linework" or "method=structure".
 *
 * @see https://postgis.net/docs/ST_MakeValid.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_MAKEVALID(g.geometry1) FROM Entity g"
 * @example Using it in DQL with params: "SELECT ST_MAKEVALID(g.geometry1, 'method=linework') FROM Entity g"
 */
class ST_MakeValid extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'ST_MakeValid';
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
