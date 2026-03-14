<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL RANGE_AGG().
 *
 * Computes the union of the non-null input values, producing a multirange.
 *
 * @see https://www.postgresql.org/docs/18/functions-range.html#RANGE-AGGREGATE-FUNCTIONS
 * @since 4.4
 *
 * @example Using it in DQL: "SELECT RANGE_AGG(e.dateRange) FROM Entity e"
 */
class RangeAgg extends BaseAggregateFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('range_agg(%s%s%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
