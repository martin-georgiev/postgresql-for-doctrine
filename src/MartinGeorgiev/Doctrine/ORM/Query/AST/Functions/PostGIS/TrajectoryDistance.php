<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS trajectory distance operator (using |=|).
 *
 * Returns the distance between A and B trajectories at their closest point of approach.
 * Trajectories are linear geometries with increasing measures (M value) on each coordinate.
 *
 * @see https://postgis.net/docs/reference.html#Operators_Distance
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT TRAJECTORY_DISTANCE(t1.trajectory, t2.trajectory) FROM Entity t1, Entity t2"
 */
class TrajectoryDistance extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s |=| %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
