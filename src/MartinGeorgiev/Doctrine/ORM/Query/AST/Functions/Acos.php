<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL acos() function.
 *
 * Returns the inverse cosine of the argument (result in radians).
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ACOS(e.value) FROM Entity e"
 */
class Acos extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'ACOS';
    }
}
