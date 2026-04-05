<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL erf() function.
 *
 * Returns the error function of the argument.
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ERF(e.decimal1) FROM Entity e"
 */
class Erf extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'ERF';
    }
}
