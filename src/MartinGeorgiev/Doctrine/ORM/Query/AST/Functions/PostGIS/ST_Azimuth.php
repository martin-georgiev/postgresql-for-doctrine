<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Azimuth() function.
 *
 * Returns the azimuth between two points.
 * Azimuth is the angle in radians from north (0) clockwise.
 *
 * @see https://postgis.net/docs/ST_Azimuth.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_AZIMUTH(g.geometry1, g.geometry2) FROM Entity g"
 */
class ST_Azimuth extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Azimuth(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
