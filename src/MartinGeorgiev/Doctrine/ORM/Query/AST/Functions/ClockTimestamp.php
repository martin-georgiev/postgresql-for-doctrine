<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL CLOCK_TIMESTAMP().
 *
 * Returns the current timestamp.
 *
 * @see https://www.postgresql.org/docs/17/functions-datetime.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT CLOCK_TIMESTAMP() FROM Entity e"
 */
class ClockTimestamp extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'clock_timestamp';
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
