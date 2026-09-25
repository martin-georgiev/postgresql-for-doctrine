<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL PERCENT_RANK().
 *
 * Returns the relative rank of the current row, (rank - 1) / (partition rows - 1), from 0 to 1.
 *
 * @see https://www.postgresql.org/docs/18/functions-window.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT OVER(PERCENT_RANK(), ORDER BY e.score DESC) FROM Entity e"
 */
class PercentRank extends BaseArithmeticFunction implements WindowFunction
{
    protected function getFunctionName(): string
    {
        return 'percent_rank';
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
