<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL DATE_SUBTRACT().
 *
 * Subtracts an interval from a timestamp with time zone, computing times of day and daylight-savings
 * adjustments according to the time zone.
 *
 * @see https://www.postgresql.org/docs/16/functions-datetime.html
 * @since 3.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT DATE_SUBTRACT(e.timestampWithTz, '1 day', 'Europe/Sofia') FROM Entity e"
 */
class DateSubtract extends BaseVariadicFunctionWithOptionalTimezone
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'date_subtract';
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
