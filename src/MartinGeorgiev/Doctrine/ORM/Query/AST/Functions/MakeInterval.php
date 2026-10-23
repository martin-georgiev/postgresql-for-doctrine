<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL MAKE_INTERVAL().
 *
 * Creates an interval from years, months, weeks, days, hours, minutes and seconds, each optional.
 * DQL has no named arguments, so the values are passed by position: pass zeros up to the one you need.
 *
 * @see https://www.postgresql.org/docs/18/functions-datetime.html
 * @since 4.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT MAKE_INTERVAL(0, 0, 0, e.days) FROM Entity e"
 */
class MakeInterval extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'make_interval';
    }

    protected function getMinArgumentCount(): int
    {
        return 0;
    }

    protected function getMaxArgumentCount(): int
    {
        return 7;
    }
}
