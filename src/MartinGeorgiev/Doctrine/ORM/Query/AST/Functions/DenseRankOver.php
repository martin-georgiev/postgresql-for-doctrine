<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL DENSE_RANK() window function.
 *
 * Returns the rank of the current row without gaps, effectively counting peer groups.
 *
 * @see https://www.postgresql.org/docs/18/functions-window.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT DENSE_RANK_OVER(ORDER BY e.score DESC) FROM Entity e"
 */
class DenseRankOver extends BaseWindowFunction
{
    protected function getFunctionName(): string
    {
        return 'dense_rank';
    }
}
