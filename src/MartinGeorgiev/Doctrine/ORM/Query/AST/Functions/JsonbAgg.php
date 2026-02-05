<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSONB_AGG().
 *
 * Aggregates values into a JSONB array.
 *
 * @see https://www.postgresql.org/docs/9.5/functions-aggregate.html
 * @since 1.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSONB_AGG(e.value) FROM Entity e"
 */
class JsonbAgg extends BaseAggregateFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('jsonb_agg(%s%s%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
