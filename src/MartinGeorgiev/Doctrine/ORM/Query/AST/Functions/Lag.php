<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL LAG().
 *
 * Returns the value at the row a given offset before the current row within the partition, or a default when there is no such row.
 *
 * @see https://www.postgresql.org/docs/18/functions-window.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT OVER(LAG(e.price, 1, 0), PARTITION BY e.product ORDER BY e.day) FROM Entity e"
 */
class Lag extends BaseVariadicFunction implements WindowFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['SimpleArithmeticExpression,SimpleArithmeticExpression,NewValue'];
    }

    protected function getFunctionName(): string
    {
        return 'lag';
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }

    protected function getMaxArgumentCount(): int
    {
        return 3;
    }
}
