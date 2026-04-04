<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL GENERATE_SERIES() for timestamp and timestamptz types.
 *
 * Generates a set of timestamps from start to stop using a required interval step.
 * An optional fourth argument specifies the output timezone for timestamptz inputs (PostgreSQL 16+).
 *
 * @see https://www.postgresql.org/docs/18/functions-srf.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT GENERATE_TIME_SERIES(e.startDate, e.endDate, '1 day') FROM Entity e"
 * @example Using it in DQL: "SELECT GENERATE_TIME_SERIES(e.startTz, e.endTz, '1 hour', 'UTC') FROM Entity e"
 */
class GenerateTimeSeries extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'generate_series';
    }

    protected function getMinArgumentCount(): int
    {
        return 3;
    }

    protected function getMaxArgumentCount(): int
    {
        return 4;
    }
}
