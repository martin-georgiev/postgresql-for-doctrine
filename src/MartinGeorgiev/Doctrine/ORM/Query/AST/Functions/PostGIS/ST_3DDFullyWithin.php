<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_3DDFullyWithin() function.
 *
 * Tests if two 3D geometries are entirely within a given 3D distance.
 *
 * @see https://postgis.net/docs/ST_3DDFullyWithin.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "WHERE ST_3DDFullyWithin(g.geometry1, g.geometry2, 1000) = TRUE"
 */
class ST_3DDFullyWithin extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_3DDFullyWithin(%s, %s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
    }
}
