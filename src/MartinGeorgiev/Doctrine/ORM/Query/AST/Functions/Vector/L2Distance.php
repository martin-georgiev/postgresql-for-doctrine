<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Vector;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of pgvector L2 (Euclidean) distance function.
 *
 * Returns the L2 distance between two vectors. A smaller value indicates greater similarity.
 * Wraps the l2_distance(vector, vector) SQL function (equivalent to the <-> operator).
 *
 * @see https://github.com/pgvector/pgvector
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class L2Distance extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('l2_distance(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
