<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL TO_OCT().
 *
 * Writes an integer in octal. A negative number is written as its two's complement.
 *
 * @see https://www.postgresql.org/docs/18/functions-string.html
 * @since 4.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT TO_OCT(e.id) FROM Entity e"
 */
class ToOct extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'to_oct';
    }
}
