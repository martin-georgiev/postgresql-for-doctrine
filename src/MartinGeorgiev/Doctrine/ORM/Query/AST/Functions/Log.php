<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL LOG() - logarithm with specified base.
 *
 * @see https://www.postgresql.org/docs/17/functions-math.html
 * @since 3.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Log extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'LOG';
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }

    protected function getMaxArgumentCount(): int
    {
        return 2;
    }
}
