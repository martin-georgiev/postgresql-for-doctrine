<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL EXP() - exponential function (e^x).
 *
 * @see https://www.postgresql.org/docs/17/functions-math.html
 * @since 3.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Exp extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'EXP';
    }
}
