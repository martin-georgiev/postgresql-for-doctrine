<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL div() function.
 *
 * Returns the integer quotient of y/x, truncated towards zero.
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT DIV(e.integer1, e.integer2) FROM Entity e"
 */
class Div extends BaseDyadicArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'DIV';
    }
}
