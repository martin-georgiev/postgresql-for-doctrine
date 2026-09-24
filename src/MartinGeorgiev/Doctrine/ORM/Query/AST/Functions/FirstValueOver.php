<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL FIRST_VALUE() window function.
 *
 * Returns the value evaluated at the first row of the window frame.
 *
 * @see https://www.postgresql.org/docs/18/functions-window.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT FIRST_VALUE_OVER(e.price, PARTITION BY e.product ORDER BY e.day) FROM Entity e"
 */
class FirstValueOver extends BaseWindowFunction
{
    protected function customizeFunction(): void
    {
        $this->addNodeMapping('SimpleArithmeticExpression');
    }

    protected function getFunctionName(): string
    {
        return 'first_value';
    }
}
