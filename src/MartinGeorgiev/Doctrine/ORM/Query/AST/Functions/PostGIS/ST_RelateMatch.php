<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_RelateMatch().
 *
 * Tests if a DE-9IM Intersection Matrix matches an Intersection Matrix pattern.
 *
 * @see https://postgis.net/docs/ST_RelateMatch.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "WHERE ST_RelateMatch(ST_Relate(g.geometry1, g.geometry2), 'T*T***T**') = TRUE"
 */
class ST_RelateMatch extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_RelateMatch(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
