<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL lcm() function.
 *
 * Returns the least common multiple of the two arguments. The result is always non-negative.
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT LCM(e.integer1, e.integer2) FROM Entity e"
 */
class Lcm extends BaseDyadicArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'LCM';
    }
}
