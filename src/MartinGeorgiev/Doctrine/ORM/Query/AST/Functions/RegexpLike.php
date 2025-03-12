<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL REGEXP_LIKE().
 *
 * @see https://www.postgresql.org/docs/15/functions-matching.html#FUNCTIONS-POSIX-REGEXP
 * @since 2.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class RegexpLike extends BaseRegexpFunction
{
    protected function getFunctionName(): string
    {
        return 'regexp_like';
    }

    protected function getParameterCount(): int
    {
        return 2;
    }
}
