<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL REPEAT().
 *
 * Repeats a string a specified number of times.
 *
 * @see https://www.postgresql.org/docs/17/functions-string.html
 * @since 4.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT REPEAT(e.text1, 2) FROM Entity e"
 */
class Repeat extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('repeat(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('SimpleArithmeticExpression');
    }
}
