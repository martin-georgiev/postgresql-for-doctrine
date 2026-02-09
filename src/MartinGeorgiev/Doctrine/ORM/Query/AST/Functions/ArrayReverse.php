<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_REVERSE().
 *
 * Reverses the order of elements in an array.
 *
 * @see https://www.postgresql.org/docs/18/functions-array.html
 * @since 3.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_REVERSE(e.array) FROM Entity e"
 */
class ArrayReverse extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_reverse(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
