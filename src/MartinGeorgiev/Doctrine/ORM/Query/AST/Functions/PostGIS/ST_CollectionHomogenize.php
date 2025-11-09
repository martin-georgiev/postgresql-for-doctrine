<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_CollectionHomogenize() function.
 *
 * Homogenizes a geometry collection.
 * Returns the simplest representation of the collection.
 *
 * @see https://postgis.net/docs/ST_CollectionHomogenize.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_COLLECTIONHOMOGENIZE(g.geometry) FROM Entity g"
 * Returns homogenized geometry.
 */
class ST_CollectionHomogenize extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_CollectionHomogenize(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
