<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_LineSubstring().
 *
 * Returns the portion of a line between two fractional locations.
 *
 * @see https://postgis.net/docs/ST_LineSubstring.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_LINESUBSTRING(g.geometry, 0.2, 0.8) FROM Entity g"
 */
class ST_LineSubstring extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_LineSubstring(%s, %s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
    }
}
