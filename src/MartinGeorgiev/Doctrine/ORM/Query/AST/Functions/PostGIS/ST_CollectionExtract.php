<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_CollectionExtract() function.
 *
 * Extracts a specific type from a geometry collection.
 * Returns a collection containing only geometries of the specified type.
 *
 * @see https://postgis.net/docs/ST_CollectionExtract.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_COLLECTIONEXTRACT(g.geometry, 1) FROM Entity g"
 */
class ST_CollectionExtract extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_CollectionExtract(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('Literal');
    }
}
