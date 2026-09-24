<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL NTH_VALUE() window function.
 *
 * Returns the value evaluated at the n-th row of the window frame, counting from 1, or NULL when there is no such
 * row. With ORDER BY, the default frame ends at the current row's last peer, not at the end of the partition.
 *
 * @see https://www.postgresql.org/docs/18/functions-window.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT NTH_VALUE_OVER(e.price, 2, PARTITION BY e.product ORDER BY e.day) FROM Entity e"
 */
class NthValueOver extends BaseWindowFunction
{
    protected function customizeFunction(): void
    {
        $this->addNodeMapping('SimpleArithmeticExpression');
        $this->addNodeMapping('ArithmeticPrimary');
    }

    protected function getFunctionName(): string
    {
        return 'nth_value';
    }
}
