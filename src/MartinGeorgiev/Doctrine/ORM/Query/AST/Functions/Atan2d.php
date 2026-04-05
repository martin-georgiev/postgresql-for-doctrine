<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL atan2d() function.
 *
 * Returns the inverse tangent of y/x (result in degrees), using the signs of the two arguments
 * to determine the quadrant of the result.
 *
 * @see https://www.postgresql.org/docs/18/functions-math.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ATAN2D(e.y, e.x) FROM Entity e"
 */
class Atan2d extends BaseDyadicArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'ATAN2D';
    }
}
