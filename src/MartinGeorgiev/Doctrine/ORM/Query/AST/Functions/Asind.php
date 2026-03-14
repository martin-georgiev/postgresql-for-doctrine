<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL asind() function.
 *
 * Returns the inverse sine of the argument (result in degrees).
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ASIND(e.value) FROM Entity e"
 */
class Asind extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'ASIND';
    }
}
