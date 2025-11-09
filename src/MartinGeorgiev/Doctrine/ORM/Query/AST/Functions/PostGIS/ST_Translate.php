<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_Translate() function.
 *
 * Translates a geometry by the given offsets.
 * Moves the geometry in X, Y, and optionally Z directions.
 *
 * @see https://postgis.net/docs/ST_Translate.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_TRANSLATE(g.geometry, 10, 20) FROM Entity g"
 * Returns translated geometry.
 */
class ST_Translate extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_Translate(%s, %s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('Literal');
        $this->addNodeMapping('Literal');
    }
}
