<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_LineToCurve() function.
 *
 * Converts linear geometries to curved geometries where possible.
 * Attempts to convert LineString to CircularString where appropriate.
 *
 * @see https://postgis.net/docs/ST_LineToCurve.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_LINETOCURVE(g.geometry) FROM Entity g"
 */
class ST_LineToCurve extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_LineToCurve(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
