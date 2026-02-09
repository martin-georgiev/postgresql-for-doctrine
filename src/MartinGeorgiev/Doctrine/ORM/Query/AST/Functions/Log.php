<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL LOG().
 *
 * Calculates the logarithm of a number with a specified base.
 *
 * @see https://www.postgresql.org/docs/17/functions-math.html
 * @since 3.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT LOG(e.value, 10) FROM Entity e"
 */
class Log extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'LOG';
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
