<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL RANGE_INTERSECT_AGG().
 *
 * Computes the intersection of the non-null input values.
 *
 * @see https://www.postgresql.org/docs/18/functions-range.html#RANGE-AGGREGATE-FUNCTIONS
 * @since 4.4
 *
 * @example Using it in DQL: "SELECT RANGE_INTERSECT_AGG(e.dateRange) FROM Entity e"
 */
class RangeIntersectAgg extends BaseAggregateFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('range_intersect_agg(%s%s%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
