<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL LEAD() window function.
 *
 * Returns the value evaluated at the row that is offset rows after the current row within the partition,
 * or the default (NULL unless given) when there is no such row.
 *
 * @see https://www.postgresql.org/docs/18/functions-window.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT LEAD_OVER(e.price, 1, 0, PARTITION BY e.product ORDER BY e.day) FROM Entity e"
 */
class LeadOver extends BaseWindowFunction
{
    protected function customizeFunction(): void
    {
        $this->addNodeMapping('SimpleArithmeticExpression');
        $this->addNodeMapping('ArithmeticPrimary');
        $this->addNodeMapping('NewValue');
    }

    protected function getFunctionName(): string
    {
        return 'lead';
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }
}
