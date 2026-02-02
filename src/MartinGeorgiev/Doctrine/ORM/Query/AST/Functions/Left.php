<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL LEFT().
 *
 * @see https://www.postgresql.org/docs/17/functions-string.html
 * @since 4.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT LEFT(e.text1, 3) FROM Entity e"
 */
class Left extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('left(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('SimpleArithmeticExpression');
    }
}
