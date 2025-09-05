<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL nlevel function.
 *
 * Returns number of labels in path.
 *
 * @see https://www.postgresql.org/docs/current/ltree.html#LTREE-FUNCTIONS
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT NLEVEL(e.path) FROM Entity e"
 * Returns integer, number of labels in path.
 */
class Nlevel extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('nlevel(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
