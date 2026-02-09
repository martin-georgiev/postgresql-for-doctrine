<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL's distance operator (using `<@>`).
 *
 * Calculates the distance between two points.
 *
 * @see https://www.postgresql.org/docs/17/earthdistance.html#EARTHDISTANCE-POINT-BASED
 * @since 3.1
 *
 * @author Sébastien Jean <sebastien.jean76@gmail.com>
 *
 * @example Using it in DQL: "SELECT DISTANCE(e.point1, e.point2) FROM Entity e"
 */
class Distance extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s <@> %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
