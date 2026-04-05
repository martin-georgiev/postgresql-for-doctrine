<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL random_normal() function.
 *
 * Returns a random value from the normal distribution with the given parameters.
 * Default mean is 0.0 and default standard deviation is 1.0.
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT RANDOM_NORMAL() FROM Entity e"
 * @example Using it in DQL: "SELECT RANDOM_NORMAL(0, 1) FROM Entity e"
 */
class RandomNormal extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'RANDOM_NORMAL';
    }

    protected function getMinArgumentCount(): int
    {
        return 0;
    }

    protected function getMaxArgumentCount(): int
    {
        return 2;
    }
}
