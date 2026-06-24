<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_X().
 *
 * Returns the X coordinate of a point geometry.
 *
 * @see https://postgis.net/docs/ST_X.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_X(g.geometry) FROM Entity g"
 */
class ST_X extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_X(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
