<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL RANK().
 *
 * Returns the rank of the current row with gaps: peers share a rank, and the next rank skips ahead by their number.
 *
 * @see https://www.postgresql.org/docs/18/functions-window.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT OVER(RANK(), ORDER BY e.score DESC) FROM Entity e"
 */
class Rank extends BaseArithmeticFunction implements WindowFunction
{
    protected function getFunctionName(): string
    {
        return 'rank';
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
