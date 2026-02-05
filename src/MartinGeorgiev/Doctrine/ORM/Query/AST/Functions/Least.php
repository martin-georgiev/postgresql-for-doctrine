<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL LEAST().
 *
 * Returns the smallest value from a list of arguments.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-conditional.html#FUNCTIONS-GREATEST-LEAST
 * @since 0.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT LEAST(e.value1, e.value2, e.value3) FROM Entity e"
 */
class Least extends BaseComparisonFunction
{
    protected function getFunctionName(): string
    {
        return 'least';
    }
}
