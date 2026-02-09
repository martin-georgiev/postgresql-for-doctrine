<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL SIGN().
 *
 * Returns the sign of a number (-1, 0, or 1).
 *
 * @see https://www.postgresql.org/docs/17/functions-math.html
 * @since 3.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT SIGN(e.value) FROM Entity e"
 */
class Sign extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'SIGN';
    }
}
