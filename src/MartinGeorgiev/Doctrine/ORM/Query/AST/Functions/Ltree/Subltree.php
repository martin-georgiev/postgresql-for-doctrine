<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL subltree function.
 *
 * Returns subpath of ltree from position start to position end-1 (counting from 0).
 *
 * @see https://www.postgresql.org/docs/17/ltree.html#LTREE-FUNCTIONS
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT SUBLTREE(e.path, 1, 2) FROM Entity e"
 * Returns ltree, subpath from position start to position end-1.
 */
class Subltree extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('subltree(%s, %s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('SimpleArithmeticExpression');
        $this->addNodeMapping('SimpleArithmeticExpression');
    }
}
