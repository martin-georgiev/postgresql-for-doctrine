<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL NTILE() window function.
 *
 * Returns an integer ranging from 1 to the argument value, dividing the partition as equally as possible.
 *
 * @see https://www.postgresql.org/docs/18/functions-window.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT NTILE_OVER(4, PARTITION BY e.category ORDER BY e.score) FROM Entity e"
 */
class NtileOver extends BaseWindowFunction
{
    protected function customizeFunction(): void
    {
        $this->addNodeMapping('ArithmeticPrimary');
    }

    protected function getFunctionName(): string
    {
        return 'ntile';
    }
}
