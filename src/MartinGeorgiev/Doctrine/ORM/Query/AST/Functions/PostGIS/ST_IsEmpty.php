<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_IsEmpty().
 *
 * Returns true if the geometry is an empty geometry.
 *
 * @see https://postgis.net/docs/ST_IsEmpty.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with boolean comparison: "WHERE ST_ISEMPTY(g.geometry) = TRUE"
 */
class ST_IsEmpty extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_IsEmpty(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
