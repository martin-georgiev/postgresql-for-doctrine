<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL LAST_VALUE().
 *
 * Returns the value at the last row of the window frame.
 *
 * @see https://www.postgresql.org/docs/18/functions-window.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT OVER(LAST_VALUE(e.price), PARTITION BY e.product ORDER BY e.day ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING) FROM Entity e"
 */
class LastValue extends BaseFunction implements WindowFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('last_value(%s)');
        $this->addNodeMapping('SimpleArithmeticExpression');
    }
}
