<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL CUME_DIST() window function.
 *
 * Returns the cumulative distribution, i.e. the fraction of partition rows preceding or peer with the current row.
 *
 * @see https://www.postgresql.org/docs/18/functions-window.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT CUME_DIST_OVER(PARTITION BY e.category ORDER BY e.score) FROM Entity e"
 */
class CumeDistOver extends BaseWindowFunction
{
    protected function getFunctionName(): string
    {
        return 'cume_dist';
    }
}
