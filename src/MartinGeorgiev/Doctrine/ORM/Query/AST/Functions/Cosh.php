<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL cosh() function.
 *
 * Returns the hyperbolic cosine of the argument.
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT COSH(e.value) FROM Entity e"
 */
class Cosh extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'COSH';
    }
}
