<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Z().
 *
 * Returns the Z coordinate of a 3D point geometry.
 *
 * @see https://postgis.net/docs/ST_Z.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_Z(g.geometry) FROM Entity g"
 */
class ST_Z extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Z(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
