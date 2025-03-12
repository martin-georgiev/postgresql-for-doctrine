<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql REGEXP_REPLACE().
 *
 * @see https://www.postgresql.org/docs/15/functions-matching.html#FUNCTIONS-POSIX-REGEXP
 * @since 2.5.0
 *
 * @author Colin Doig
 */
class RegexpReplace extends BaseRegexpFunction
{
    protected function getFunctionName(): string
    {
        return 'regexp_replace';
    }

    protected function getParameterCount(): int
    {
        return 3;
    }
}
