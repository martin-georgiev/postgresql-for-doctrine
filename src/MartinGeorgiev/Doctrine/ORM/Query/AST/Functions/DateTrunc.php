<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL DATE_TRUNC().
 *
 * Truncates a timestamp to a specified precision.
 *
 * @see https://www.postgresql.org/docs/16/functions-datetime.html#FUNCTIONS-DATETIME-TRUNC
 * @since 3.7
 *
 * @author Jan Klan <jan@klan.com.au>
 *
 * @example Using it in DQL: "SELECT DATE_TRUNC('day', e.timestampWithTz, 'Australia/Adelaide') FROM Entity e"
 */
class DateTrunc extends BaseVariadicFunctionWithOptionalTimezone
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'date_trunc';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 3;
    }
}
