<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_REPLACE().
 *
 * Replaces all occurrences of a value in an array with a new value.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-array.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_REPLACE(e.array, 5, 10) FROM Entity e"
 */
class ArrayReplace extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_replace(%s, %s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('NewValue');
        $this->addNodeMapping('NewValue');
    }
}
