<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_LOWER().
 *
 * Returns the lower bound of a specified dimension of an array.
 *
 * @see https://www.postgresql.org/docs/17/functions-array.html
 * @since 4.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_LOWER(e.tags, 1) FROM Entity e"
 */
class ArrayLower extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_lower(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
    }
}
