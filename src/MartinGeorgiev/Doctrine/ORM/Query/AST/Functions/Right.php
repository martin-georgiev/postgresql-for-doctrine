<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL RIGHT().
 *
 * @see https://www.postgresql.org/docs/17/functions-string.html
 * @since 4.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT RIGHT(e.text1, 6) FROM Entity e"
 */
class Right extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('right(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('SimpleArithmeticExpression');
    }
}
