<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL erfc() function.
 *
 * Returns the complementary error function of the argument (1 - erf(x)).
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ERFC(e.decimal1) FROM Entity e"
 */
class Erfc extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'ERFC';
    }
}
