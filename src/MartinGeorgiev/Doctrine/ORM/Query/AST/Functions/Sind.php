<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL sind() function.
 *
 * Returns the sine of the argument (given in degrees).
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT SIND(e.degrees) FROM Entity e"
 */
class Sind extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'SIND';
    }
}
