<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_3DLength() function.
 *
 * Returns the 3D length of the geometry if it is a LineString or MultiLineString.
 * For areal geometries, the 3D perimeter is returned.
 *
 * @see https://postgis.net/docs/ST_3DLength.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_3DLENGTH(g.geometry) FROM Entity g"
 */
class ST_3DLength extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_3DLength(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
