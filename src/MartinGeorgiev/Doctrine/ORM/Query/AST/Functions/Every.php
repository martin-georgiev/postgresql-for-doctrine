<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL EVERY().
 *
 * SQL standard alias for bool_and aggregate.
 *
 * @see https://www.postgresql.org/docs/17/functions-aggregate.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT EVERY(e.field) FROM Entity e"
 */
class Every extends BaseAggregateFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('every(%s%s%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
