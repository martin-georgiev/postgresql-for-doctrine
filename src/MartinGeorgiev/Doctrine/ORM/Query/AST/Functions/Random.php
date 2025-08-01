<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL RANDOM() - returns a random value between 0.0 and 1.0.
 *
 * @see https://www.postgresql.org/docs/17/functions-math.html
 * @since 3.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Random extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'RANDOM';
    }

    protected function getMaxArgumentCount(): int
    {
        return 0; // RANDOM() takes no arguments
    }

    protected function getMinArgumentCount(): int
    {
        return 0; // RANDOM() takes no arguments
    }
}
