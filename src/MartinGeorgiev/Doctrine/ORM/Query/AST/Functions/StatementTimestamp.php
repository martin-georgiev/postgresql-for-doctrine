<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL STATEMENT_TIMESTAMP().
 *
 * Returns the start time of the current statement.
 *
 * @see https://www.postgresql.org/docs/17/functions-datetime.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT STATEMENT_TIMESTAMP() FROM Entity e"
 */
class StatementTimestamp extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'statement_timestamp';
    }

    protected function getMinArgumentCount(): int
    {
        return 0;
    }

    protected function getMaxArgumentCount(): int
    {
        return 0;
    }
}
