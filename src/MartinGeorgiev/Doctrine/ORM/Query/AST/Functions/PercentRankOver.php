<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL PERCENT_RANK() window function.
 *
 * Returns the relative rank of the current row, (rank - 1) / (total partition rows - 1).
 *
 * @see https://www.postgresql.org/docs/18/functions-window.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT PERCENT_RANK_OVER(PARTITION BY e.category ORDER BY e.score) FROM Entity e"
 */
class PercentRankOver extends BaseWindowFunction
{
    protected function getFunctionName(): string
    {
        return 'percent_rank';
    }
}
