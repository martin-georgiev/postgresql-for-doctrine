<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_DWithin() function.
 *
 * Tests if two geometries are within a given distance.
 *
 * @see https://postgis.net/docs/ST_DWithin.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "WHERE ST_DWithin(g.geometry1, g.geometry2, 1000) = TRUE"
 * Returns boolean, must be used with "= TRUE" or "= FALSE" in DQL.
 */
class ST_DWithin extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_DWithin(%s, %s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
    }
}
