<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_MakeEnvelope().
 *
 * Creates a rectangular polygon from minimum and maximum coordinates, with an optional SRID.
 *
 * @see https://postgis.net/docs/ST_MakeEnvelope.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_MAKEENVELOPE(0, 0, 1, 1) FROM Entity g"
 * @example Using it in DQL with srid: "SELECT ST_MAKEENVELOPE(0, 0, 1, 1, 4326) FROM Entity g"
 */
class ST_MakeEnvelope extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['ArithmeticPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'ST_MakeEnvelope';
    }

    protected function getMinArgumentCount(): int
    {
        return 4;
    }

    protected function getMaxArgumentCount(): int
    {
        return 5;
    }
}
