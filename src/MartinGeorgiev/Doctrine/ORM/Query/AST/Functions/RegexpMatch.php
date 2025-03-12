<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL REGEXP_MATCH().
 *
 * @see https://www.postgresql.org/docs/15/functions-matching.html#FUNCTIONS-POSIX-REGEXP
 * @since 2.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class RegexpMatch extends BaseRegexpFunction
{
    protected function getFunctionName(): string
    {
        return 'regexp_match';
    }

    protected function getParameterCount(): int
    {
        return 2;
    }
}
