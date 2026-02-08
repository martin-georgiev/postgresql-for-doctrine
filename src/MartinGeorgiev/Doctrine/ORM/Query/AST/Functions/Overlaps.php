<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL && operator.
 *
 * Checks if left side overlaps with right side.
 *
 * @see https://www.postgresql.org/docs/9.6/static/functions-array.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with boolean comparison: "WHERE OVERLAPS(e.textArray, e.integerArray) = TRUE"
 * Returns boolean, must be used with "= TRUE" or "= FALSE" in DQL.
 */
class Overlaps extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s && %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
