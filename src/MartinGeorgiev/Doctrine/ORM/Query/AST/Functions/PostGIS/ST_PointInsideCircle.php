<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_PointInsideCircle() function.
 *
 * Tests if a point geometry is inside a circle defined by a center and radius.
 *
 * @see https://postgis.net/docs/ST_PointInsideCircle.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "WHERE ST_PointInsideCircle(g.point, 0, 0, 1000) = TRUE"
 */
class ST_PointInsideCircle extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_PointInsideCircle(%s, %s, %s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
    }
}
