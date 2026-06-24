<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_NumGeometries().
 *
 * Returns the number of elements in a geometry collection.
 *
 * @see https://postgis.net/docs/ST_NumGeometries.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_NUMGEOMETRIES(g.geometry) FROM Entity g"
 */
class ST_NumGeometries extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_NumGeometries(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
