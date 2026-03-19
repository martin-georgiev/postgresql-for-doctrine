<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL GENERATE_SERIES().
 *
 * Generates a set of values from start to stop, with an optional step.
 *
 * @see https://www.postgresql.org/docs/18/functions-srf.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT GENERATE_SERIES(e.date1, e.date2, '1 day') FROM Entity e"
 * @example Using it in DQL: "SELECT GENERATE_SERIES(e.date1, e.date2) FROM Entity e"
 */
class GenerateSeries extends BaseVariadicFunction
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
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 3;
    }
}
