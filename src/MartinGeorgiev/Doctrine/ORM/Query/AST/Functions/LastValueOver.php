<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL LAST_VALUE() window function.
 *
 * Returns the value evaluated at the last row of the window frame. With ORDER BY, the default frame ends at the
 * current row's last peer, not at the end of the partition.
 *
 * @see https://www.postgresql.org/docs/18/functions-window.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT LAST_VALUE_OVER(e.price, PARTITION BY e.product) FROM Entity e"
 */
class LastValueOver extends BaseWindowFunction
{
    protected function customizeFunction(): void
    {
        $this->addNodeMapping('SimpleArithmeticExpression');
    }

    protected function getFunctionName(): string
    {
        return 'last_value';
    }
}
