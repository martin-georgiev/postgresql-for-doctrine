<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL PI() - returns the mathematical constant π.
 *
 * @see https://www.postgresql.org/docs/17/functions-math.html
 * @since 3.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Pi extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'PI';
    }

    protected function getMaxArgumentCount(): int
    {
        return 0; // PI() takes no arguments
    }

    protected function getMinArgumentCount(): int
    {
        return 0; // PI() takes no arguments
    }
}
