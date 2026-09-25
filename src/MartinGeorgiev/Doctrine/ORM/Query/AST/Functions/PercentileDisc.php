<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL PERCENTILE_DISC().
 *
 * Returns the first ordered value whose position in the ordering equals or exceeds the fraction.
 *
 * @see https://www.postgresql.org/docs/18/functions-aggregate.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT PERCENTILE_DISC(0.9 WITHIN GROUP ORDER BY e.price DESC) FROM Entity e"
 */
class PercentileDisc extends BaseOrderedSetAggregateFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('percentile_disc(%s) WITHIN GROUP (%s)');
        $this->addNodeMapping('ArithmeticPrimary');
    }
}
