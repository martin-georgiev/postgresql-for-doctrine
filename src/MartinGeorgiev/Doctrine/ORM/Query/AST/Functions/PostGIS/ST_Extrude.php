<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Extrude() function.
 *
 * Extrudes a 2D geometry to 3D.
 * Useful for creating 3D representations of 2D geometries.
 *
 * @see https://postgis.net/docs/ST_Extrude.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_EXTRUDE(g.geometry, 0, 0, 10) FROM Entity g"
 * Returns 3D extruded geometry.
 */
class ST_Extrude extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Extrude(%s, %s, %s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('Literal');
        $this->addNodeMapping('Literal');
        $this->addNodeMapping('Literal');
    }
}
