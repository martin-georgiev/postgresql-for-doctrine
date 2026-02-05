<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL CEIL().
 *
 * Rounds a number up to the nearest integer.
 *
 * @see https://www.postgresql.org/docs/17/functions-math.html
 * @since 3.2
 *
 * @author Jan Klan <jan@klan.com.au>
 *
 * @example Using it in DQL: "SELECT CEIL(e.value) FROM Entity e"
 */
class Ceil extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'CEIL';
    }
}
