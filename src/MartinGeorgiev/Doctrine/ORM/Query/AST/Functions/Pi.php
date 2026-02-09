<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL PI().
 *
 * Returns the mathematical constant π.
 *
 * @see https://www.postgresql.org/docs/17/functions-math.html
 * @since 3.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT PI() FROM Entity e"
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
