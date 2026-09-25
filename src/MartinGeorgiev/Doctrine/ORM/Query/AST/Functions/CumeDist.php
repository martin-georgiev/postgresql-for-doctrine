<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL CUME_DIST().
 *
 * Returns the cumulative distribution of the current row: the fraction of partition rows that precede it or are its peers.
 *
 * @see https://www.postgresql.org/docs/18/functions-window.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT OVER(CUME_DIST(), ORDER BY e.score DESC) FROM Entity e"
 */
class CumeDist extends BaseArithmeticFunction implements WindowFunction
{
    protected function getFunctionName(): string
    {
        return 'cume_dist';
    }

    protected function getMaxArgumentCount(): int
    {
        return 0;
    }

    protected function getMinArgumentCount(): int
    {
        return 0;
    }
}
