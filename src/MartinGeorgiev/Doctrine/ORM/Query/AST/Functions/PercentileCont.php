<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL PERCENTILE_CONT().
 *
 * Returns the continuous percentile of the ordered values, interpolating between adjacent ones when needed.
 *
 * @see https://www.postgresql.org/docs/18/functions-aggregate.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT PERCENTILE_CONT(0.5 WITHIN GROUP ORDER BY e.price) FROM Entity e"
 */
class PercentileCont extends BaseOrderedSetAggregateFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('percentile_cont(%s) WITHIN GROUP (%s)');
        $this->addNodeMapping('ArithmeticPrimary');
    }
}
