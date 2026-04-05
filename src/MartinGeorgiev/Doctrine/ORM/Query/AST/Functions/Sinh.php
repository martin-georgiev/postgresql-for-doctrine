<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL sinh() function.
 *
 * Returns the hyperbolic sine of the argument.
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT SINH(e.value) FROM Entity e"
 */
class Sinh extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'SINH';
    }
}
