<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL NTH_VALUE().
 *
 * Returns the value at the n-th row of the window frame, counting from 1, or null when the frame has no such row.
 *
 * @see https://www.postgresql.org/docs/18/functions-window.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT OVER(NTH_VALUE(e.price, 2), PARTITION BY e.product ORDER BY e.day) FROM Entity e"
 */
class NthValue extends BaseFunction implements WindowFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('nth_value(%s, %s)');
        $this->addNodeMapping('SimpleArithmeticExpression');
        $this->addNodeMapping('SimpleArithmeticExpression');
    }
}
