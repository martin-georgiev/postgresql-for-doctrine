<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_SimplifyVW() function.
 *
 * Simplifies geometry using Visvalingam-Whyatt algorithm.
 * Alternative to Douglas-Peucker algorithm, often produces better results.
 *
 * @see https://postgis.net/docs/ST_SimplifyVW.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_SIMPLIFYVW(g.geometry, 0.5) FROM Entity g"
 */
class ST_SimplifyVW extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_SimplifyVW(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('Literal');
    }
}
