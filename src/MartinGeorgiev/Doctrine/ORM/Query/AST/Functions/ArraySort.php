<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_SORT().
 *
 * Sorts the elements of an array.
 *
 * @see https://www.postgresql.org/docs/18/functions-array.html
 * @since 3.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_SORT(e.array) FROM Entity e"
 */
class ArraySort extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_sort(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
