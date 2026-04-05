<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL asinh() function.
 *
 * Returns the inverse hyperbolic sine of the argument.
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ASINH(e.value) FROM Entity e"
 */
class Asinh extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'ASINH';
    }
}
