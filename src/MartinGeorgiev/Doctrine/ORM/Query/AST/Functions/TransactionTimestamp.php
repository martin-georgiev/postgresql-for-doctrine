<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL TRANSACTION_TIMESTAMP().
 *
 * Returns the start time of the current transaction.
 *
 * @see https://www.postgresql.org/docs/17/functions-datetime.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT TRANSACTION_TIMESTAMP() FROM Entity e"
 */
class TransactionTimestamp extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'transaction_timestamp';
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
