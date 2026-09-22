<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_IsClosed().
 *
 * Returns true when the start and end points of a LineString are the same.
 * For a MultiLineString this holds only when every element is closed.
 *
 * @see https://postgis.net/docs/ST_IsClosed.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with boolean comparison: "WHERE ST_ISCLOSED(g.geometry1) = TRUE"
 */
class ST_IsClosed extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_IsClosed(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
