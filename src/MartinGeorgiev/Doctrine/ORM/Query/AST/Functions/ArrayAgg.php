<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_AGG().
 *
 * Aggregates values into an array.
 *
 * @see https://www.postgresql.org/docs/17/functions-aggregate.html
 * @since 1.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_AGG(e.value) FROM Entity e"
 */
class ArrayAgg extends BaseAggregateFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_agg(%s%s%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
