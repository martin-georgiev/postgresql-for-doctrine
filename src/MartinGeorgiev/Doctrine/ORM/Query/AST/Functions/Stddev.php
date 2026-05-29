<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL STDDEV().
 *
 * Computes the sample standard deviation of a set of values.
 *
 * @see https://www.postgresql.org/docs/17/functions-aggregate.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT STDDEV(e.field) FROM Entity e"
 */
class Stddev extends BaseAggregateFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('stddev(%s%s%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
