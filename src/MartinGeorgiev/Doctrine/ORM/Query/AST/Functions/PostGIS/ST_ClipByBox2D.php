<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_ClipByBox2D() function.
 *
 * Clips a geometry by a 2D box.
 * Returns the portion of the input geometry that falls within the specified box.
 *
 * @see https://postgis.net/docs/ST_ClipByBox2D.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_CLIPBYBOX2D(g.geometry, ST_Envelope(g.geometry)) FROM Entity g"
 * Returns clipped geometry.
 */
class ST_ClipByBox2D extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_ClipByBox2D(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
