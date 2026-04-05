<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL POWER().
 *
 * Raises a number to a specified power.
 *
 * @see https://www.postgresql.org/docs/17/functions-math.html
 * @since 3.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT POWER(e.base, e.exponent) FROM Entity e"
 */
class Power extends BaseDyadicArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'POWER';
    }
}
