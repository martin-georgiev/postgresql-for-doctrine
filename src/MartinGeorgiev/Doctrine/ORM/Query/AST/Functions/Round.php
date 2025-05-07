<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ROUND().
 *
 * @see https://www.postgresql.org/docs/17/functions-math.html
 * @since 3.2
 *
 * @author Jan Klan <jan@klan.com.au>
 */
class Round extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'ROUND';
    }

    protected function getMaxArgumentCount(): int
    {
        return 2;
    }
}
