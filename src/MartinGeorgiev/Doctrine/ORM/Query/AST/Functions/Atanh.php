<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL atanh() function.
 *
 * Returns the inverse hyperbolic tangent of the argument (argument must be between -1 and 1).
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ATANH(e.value) FROM Entity e"
 */
class Atanh extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'ATANH';
    }
}
