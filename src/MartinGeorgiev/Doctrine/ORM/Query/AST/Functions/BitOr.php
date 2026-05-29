<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL BIT_OR().
 *
 * Aggregates integer values using bitwise OR.
 *
 * @see https://www.postgresql.org/docs/17/functions-aggregate.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT BIT_OR(e.field) FROM Entity e"
 */
class BitOr extends BaseAggregateFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('bit_or(%s%s%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
