<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_APPEND().
 *
 * Appends an element to the end of an array.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-array.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_APPEND(e.array, 5) FROM Entity e"
 */
class ArrayAppend extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_append(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('NewValue');
    }
}
