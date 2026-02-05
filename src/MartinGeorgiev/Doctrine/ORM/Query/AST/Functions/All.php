<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ALL().
 *
 * Evaluates to true if all subquery rows satisfy the condition.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-subquery.html#FUNCTIONS-SUBQUERY-ALL
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT * FROM Entity e WHERE e.value > ALL(SELECT value FROM OtherEntity)"
 */
class All extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ALL(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
