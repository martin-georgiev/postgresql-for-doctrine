<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_PREPEND().
 *
 * Prepends an element to the beginning of an array.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-array.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_PREPEND(0, e.array) FROM Entity e"
 */
class ArrayPrepend extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_prepend(%s, %s)');
        $this->addNodeMapping('NewValue');
        $this->addNodeMapping('StringPrimary');
    }
}
