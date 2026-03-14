<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL acosh() function.
 *
 * Returns the inverse hyperbolic cosine of the argument (argument must be >= 1).
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ACOSH(e.value) FROM Entity e"
 */
class Acosh extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'ACOSH';
    }
}
