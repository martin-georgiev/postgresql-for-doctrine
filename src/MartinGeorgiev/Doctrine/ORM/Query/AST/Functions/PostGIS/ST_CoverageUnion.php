<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_CoverageUnion() aggregate function.
 *
 * Computes the union of a set of polygons forming a coverage by removing shared edges.
 * Much faster than ST_Union when input forms a valid coverage.
 *
 * @see https://postgis.net/docs/ST_CoverageUnion.html
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_CoverageUnion(g.geometry) FROM Entity g"
 */
class ST_CoverageUnion extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_CoverageUnion(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
