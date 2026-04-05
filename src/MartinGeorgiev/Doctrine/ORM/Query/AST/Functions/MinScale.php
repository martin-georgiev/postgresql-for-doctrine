<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL min_scale() function.
 *
 * Returns the minimum number of digits needed to represent the value with full precision.
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT MIN_SCALE(e.decimal1) FROM Entity e"
 */
class MinScale extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'MIN_SCALE';
    }
}
