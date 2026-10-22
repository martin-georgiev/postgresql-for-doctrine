<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL MULTIRANGE_MINUS_MULTI().
 *
 * Returns the multirange left after subtracting the second multirange from the first, as a single row, or no row
 * at all when nothing is left.
 *
 * @see https://www.postgresql.org/docs/19/functions-range.html
 * @since 4.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT MULTIRANGE_MINUS_MULTI(e.availability, e.bookings) FROM Entity e"
 */
class MultirangeMinusMulti extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('multirange_minus_multi(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
