<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_LineCrossingDirection() function.
 *
 * Returns a number indicating the crossing behavior of two LineStrings.
 *
 * @see https://postgis.net/docs/ST_LineCrossingDirection.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_LineCrossingDirection(g.geometry1, g.geometry2) as result"
 */
class ST_LineCrossingDirection extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_LineCrossingDirection(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
