<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL RANK() window function.
 *
 * Returns the rank of the current row with gaps, i.e. the row number of the first row in its peer group.
 *
 * @see https://www.postgresql.org/docs/18/functions-window.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT RANK_OVER(ORDER BY e.score DESC) FROM Entity e"
 */
class RankOver extends BaseWindowFunction
{
    protected function getFunctionName(): string
    {
        return 'rank';
    }
}
