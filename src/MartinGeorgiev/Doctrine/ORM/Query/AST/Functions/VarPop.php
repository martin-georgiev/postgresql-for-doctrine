<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL VAR_POP().
 *
 * Computes the population variance of a set of values.
 *
 * @see https://www.postgresql.org/docs/17/functions-aggregate.html
 * @since 4.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT VAR_POP(e.field) FROM Entity e"
 */
class VarPop extends BaseAggregateFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('var_pop(%s%s%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
