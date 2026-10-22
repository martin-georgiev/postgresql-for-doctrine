<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL RANGE_MINUS_MULTI().
 *
 * Returns the non-empty ranges left after subtracting the second range from the first, one row per range, so a
 * subtraction that splits the first range in two returns two rows instead of failing like the - operator.
 *
 * @see https://www.postgresql.org/docs/19/functions-range.html
 * @since 4.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT RANGE_MINUS_MULTI(e.availability, e.booking) FROM Entity e"
 */
class RangeMinusMulti extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('range_minus_multi(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
