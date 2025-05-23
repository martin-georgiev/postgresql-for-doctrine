<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL WIDTH_BUCKET() - assigns values to buckets (equi-width histogram).
 *
 * @see https://www.postgresql.org/docs/17/functions-math.html
 * @since 3.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT WIDTH_BUCKET(operand, b1, b2, count) FROM Entity e"
 */
class WidthBucket extends BaseArithmeticFunction
{
    protected function getFunctionName(): string
    {
        return 'WIDTH_BUCKET';
    }

    protected function getMinArgumentCount(): int
    {
        return 4;
    }

    protected function getMaxArgumentCount(): int
    {
        return 4;
    }
}
