<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Union() function.
 *
 * Returns a geometry that represents the point set union of two geometries.
 * The result may be a heterogeneous geometry collection.
 *
 * @see https://postgis.net/docs/ST_Union.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_UNION(g1.geometry, g2.geometry) FROM Entity g1, Entity g2"
 * Returns geometry representing the union.
 */
class ST_Union extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Union(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
