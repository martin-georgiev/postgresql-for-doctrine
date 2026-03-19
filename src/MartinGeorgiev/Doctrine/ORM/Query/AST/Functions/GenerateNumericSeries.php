<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL GENERATE_SERIES() for integer and numeric types.
 *
 * Generates a set of values from start to stop with an optional step (defaults to 1).
 * Supports integer, bigint, and numeric types.
 *
 * @see https://www.postgresql.org/docs/18/functions-srf.html
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT GENERATE_NUMERIC_SERIES(e.start, e.stop) FROM Entity e"
 * @example Using it in DQL: "SELECT GENERATE_NUMERIC_SERIES(e.start, e.stop, e.step) FROM Entity e"
 */
class GenerateNumericSeries extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['ArithmeticPrimary'];
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
