<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL FIRST_VALUE().
 *
 * Returns the value at the first row of the window frame.
 *
 * @see https://www.postgresql.org/docs/18/functions-window.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT OVER(FIRST_VALUE(e.price), PARTITION BY e.product ORDER BY e.day) FROM Entity e"
 */
class FirstValue extends BaseFunction implements WindowFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('first_value(%s)');
        $this->addNodeMapping('SimpleArithmeticExpression');
    }
}
