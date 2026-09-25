<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL MODE().
 *
 * Returns the most frequent ordered value, choosing the first one arbitrarily when several are equally frequent.
 *
 * @see https://www.postgresql.org/docs/18/functions-aggregate.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT MODE(WITHIN GROUP ORDER BY e.category) FROM Entity e"
 */
class Mode extends BaseOrderedSetAggregateFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('mode() WITHIN GROUP (%s)');
    }
}
