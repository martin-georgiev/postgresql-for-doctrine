<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunctionWithOptionalBoolean;

/**
 * Implementation of PostGIS ST_RemoveIrrelevantPointsForView() function.
 *
 * Removes points that are irrelevant for rendering at a given view.
 * Useful for simplifying geometries for display at specific zoom levels.
 *
 * @see https://postgis.net/docs/ST_RemoveIrrelevantPointsForView.html
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_REMOVEIRRELEVANTPOINTSFORVIEW(g.geometry, box) FROM Entity g"
 * @example Using it in DQL: "SELECT ST_REMOVEIRRELEVANTPOINTSFORVIEW(g.geometry, box, 'true') FROM Entity g"
 */
class ST_RemoveIrrelevantPointsForView extends BaseVariadicFunctionWithOptionalBoolean
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,StringPrimary,StringPrimary',
            'StringPrimary,StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'ST_RemoveIrrelevantPointsForView';
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
