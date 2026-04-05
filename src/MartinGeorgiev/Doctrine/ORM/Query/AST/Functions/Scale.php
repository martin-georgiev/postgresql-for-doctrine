<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL scale() function.
 *
 * Returns the number of decimal digits in the fractional part (scale) of the argument.
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT SCALE(e.decimal1) FROM Entity e"
 */
class Scale extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'SCALE';
    }
}
