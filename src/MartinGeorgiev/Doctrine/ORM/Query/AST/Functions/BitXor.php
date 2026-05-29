<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL BIT_XOR().
 *
 * Aggregates integer values using bitwise XOR.
 *
 * @see https://www.postgresql.org/docs/17/functions-aggregate.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT BIT_XOR(e.field) FROM Entity e"
 */
class BitXor extends BaseAggregateFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('bit_xor(%s%s%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
