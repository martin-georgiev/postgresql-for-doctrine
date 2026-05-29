<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL BOOL_AND().
 *
 * Aggregates boolean values using AND logic.
 *
 * @see https://www.postgresql.org/docs/17/functions-aggregate.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT BOOL_AND(e.field) FROM Entity e"
 */
class BoolAnd extends BaseAggregateFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('bool_and(%s%s%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
