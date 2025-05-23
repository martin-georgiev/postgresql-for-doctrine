<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL POWER() function.
 *
 * @see https://www.postgresql.org/docs/17/functions-math.html
 * @since 3.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Power extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'POWER';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 2;
    }
}
