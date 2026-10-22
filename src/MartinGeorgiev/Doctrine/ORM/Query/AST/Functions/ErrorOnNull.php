<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ERROR_ON_NULL().
 *
 * Returns its argument unchanged, or fails the query with "null value not allowed" when the argument is NULL.
 *
 * @see https://www.postgresql.org/docs/19/functions-comparison.html
 * @since 4.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ERROR_ON_NULL(e.price) FROM Entity e"
 */
class ErrorOnNull extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('error_on_null(%s)');
        $this->addNodeMapping('SimpleArithmeticExpression');
    }
}
