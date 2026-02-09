<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ROUND().
 *
 * Rounds a number to a specified number of decimal places.
 *
 * @see https://www.postgresql.org/docs/17/functions-math.html
 * @since 3.2
 *
 * @author Jan Klan <jan@klan.com.au>
 *
 * @example Using it in DQL: "SELECT ROUND(e.value, 2) FROM Entity e"
 */
class Round extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'ROUND';
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }

    protected function getMaxArgumentCount(): int
    {
        return 2;
    }
}
