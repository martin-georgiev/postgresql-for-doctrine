<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL trim_scale() function.
 *
 * Reduces the value's scale (number of fractional decimal digits) by removing trailing zeroes.
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT TRIM_SCALE(e.decimal1) FROM Entity e"
 */
class TrimScale extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'TRIM_SCALE';
    }
}
