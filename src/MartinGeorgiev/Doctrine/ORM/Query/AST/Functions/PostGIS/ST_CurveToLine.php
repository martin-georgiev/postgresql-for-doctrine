<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_CurveToLine() function.
 *
 * Converts curved geometries to linear geometries.
 * Useful for converting CircularString, CompoundCurve, etc. to LineString.
 *
 * @see https://postgis.net/docs/ST_CurveToLine.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_CURVETOLINE(g.geometry) FROM Entity g"
 */
class ST_CurveToLine extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_CurveToLine(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
