<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL LAG() window function.
 *
 * Returns the value evaluated at the row that is offset rows before the current row within the partition,
 * or the default (NULL unless given) when there is no such row.
 *
 * @see https://www.postgresql.org/docs/18/functions-window.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT LAG_OVER(e.price, 1, 0, PARTITION BY e.product ORDER BY e.day) FROM Entity e"
 */
class LagOver extends BaseWindowFunction
{
    protected function customizeFunction(): void
    {
        $this->addNodeMapping('SimpleArithmeticExpression');
        $this->addNodeMapping('ArithmeticPrimary');
        $this->addNodeMapping('NewValue');
    }

    protected function getFunctionName(): string
    {
        return 'lag';
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }
}
