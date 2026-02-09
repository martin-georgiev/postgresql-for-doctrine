<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL GREATEST().
 *
 * Returns the greatest value from a list of arguments.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-conditional.html#FUNCTIONS-GREATEST-LEAST
 * @since 0.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT GREATEST(e.value1, e.value2, e.value3) FROM Entity e"
 */
class Greatest extends BaseComparisonFunction
{
    protected function getFunctionName(): string
    {
        return 'greatest';
    }
}
