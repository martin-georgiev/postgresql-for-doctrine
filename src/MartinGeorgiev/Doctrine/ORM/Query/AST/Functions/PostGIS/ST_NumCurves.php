<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_NumCurves() function.
 *
 * Returns the number of curves in a CompoundCurve.
 *
 * @see https://postgis.net/docs/ST_NumCurves.html
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_NUMCURVES(g.geometry) FROM Entity g"
 */
class ST_NumCurves extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_NumCurves(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
