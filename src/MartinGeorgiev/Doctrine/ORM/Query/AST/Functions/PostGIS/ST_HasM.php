<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_HasM() function.
 *
 * Returns true if the geometry has an M (measure) coordinate.
 *
 * @see https://postgis.net/docs/ST_HasM.html
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "WHERE ST_HasM(g.geometry) = TRUE"
 */
class ST_HasM extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_HasM(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
