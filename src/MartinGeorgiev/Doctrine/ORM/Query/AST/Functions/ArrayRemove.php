<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_REMOVE().
 *
 * Removes all occurrences of a value from an array.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-array.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_REMOVE(e.array, 5) FROM Entity e"
 */
class ArrayRemove extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_remove(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('NewValue');
    }
}
