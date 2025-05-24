<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL DEGREES() - converts radians to degrees.
 *
 * @see https://www.postgresql.org/docs/17/functions-math.html
 * @since 3.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Degrees extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'DEGREES';
    }
}
