<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ANY().
 *
 * Evaluates to true if any subquery row satisfies the condition.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-subquery.html#FUNCTIONS-SUBQUERY-ANY-SOME
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT * FROM Entity e WHERE e.value > ANY(SELECT value FROM OtherEntity)"
 */
class Any extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ANY(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
