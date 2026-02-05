<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL ltree2text function.
 *
 * Casts ltree to text.
 *
 * @see https://www.postgresql.org/docs/17/ltree.html#LTREE-FUNCTIONS
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT LTREE2TEXT(e.path) FROM Entity e"
 */
class Ltree2text extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ltree2text(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
