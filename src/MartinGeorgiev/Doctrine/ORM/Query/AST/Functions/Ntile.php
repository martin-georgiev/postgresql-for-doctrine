<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL NTILE().
 *
 * Returns the bucket, from 1 to the given count, that the current row falls in when the partition is split as equally as possible.
 *
 * @see https://www.postgresql.org/docs/18/functions-window.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT OVER(NTILE(4), ORDER BY e.score DESC) FROM Entity e"
 */
class Ntile extends BaseArithmeticFunction implements WindowFunction
{
    protected function getFunctionName(): string
    {
        return 'ntile';
    }
}
