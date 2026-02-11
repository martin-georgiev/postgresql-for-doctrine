<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_HasZ() function.
 *
 * Returns true if the geometry has a Z coordinate.
 *
 * @see https://postgis.net/docs/ST_HasZ.html
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "WHERE ST_HasZ(g.geometry) = TRUE"
 */
class ST_HasZ extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_HasZ(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
