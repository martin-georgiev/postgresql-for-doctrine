<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL TRIM_ARRAY().
 *
 * Removes the last n elements of an array.
 *
 * @see https://www.postgresql.org/docs/18/functions-array.html
 * @since 4.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT TRIM_ARRAY(e.array, 1) FROM Entity e"
 */
class TrimArray extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('trim_array(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('SimpleArithmeticExpression');
    }
}
