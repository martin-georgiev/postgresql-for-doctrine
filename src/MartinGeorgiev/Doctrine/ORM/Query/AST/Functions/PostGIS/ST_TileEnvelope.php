<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_TileEnvelope().
 *
 * Returns the bounding box polygon for a Web Mercator tile specified by zoom/x/y.
 *
 * @see https://postgis.net/docs/ST_TileEnvelope.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_TILEENVELOPE(10, 512, 384) FROM Entity g"
 * @example Using it in DQL with bounds: "SELECT ST_TILEENVELOPE(10, 512, 384, ST_MAKEENVELOPE(0, 0, 180, 90, 4326)) FROM Entity g"
 * @example Using it in DQL with margin: "SELECT ST_TILEENVELOPE(10, 512, 384, ST_MAKEENVELOPE(0, 0, 180, 90, 4326), 0.1) FROM Entity g"
 */
class ST_TileEnvelope extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'ArithmeticPrimary,ArithmeticPrimary,ArithmeticPrimary,StringPrimary,ArithmeticPrimary',
            'ArithmeticPrimary,ArithmeticPrimary,ArithmeticPrimary,StringPrimary',
            'ArithmeticPrimary,ArithmeticPrimary,ArithmeticPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'ST_TileEnvelope';
    }

    protected function getMinArgumentCount(): int
    {
        return 3;
    }

    protected function getMaxArgumentCount(): int
    {
        return 5;
    }
}
