<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL atand() function.
 *
 * Returns the inverse tangent of the argument (result in degrees).
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ATAND(e.value) FROM Entity e"
 */
class Atand extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'ATAND';
    }
}
