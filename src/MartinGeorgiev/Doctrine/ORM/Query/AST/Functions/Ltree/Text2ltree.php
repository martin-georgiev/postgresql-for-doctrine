<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL text2ltree function.
 *
 * Casts text to ltree.
 *
 * @see https://www.postgresql.org/docs/current/ltree.html#LTREE-FUNCTIONS
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT TEXT2LTREE('Top.Child1.Child2') FROM Entity e"
 * Returns ltree, converted from text.
 */
class Text2ltree extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('text2ltree(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
