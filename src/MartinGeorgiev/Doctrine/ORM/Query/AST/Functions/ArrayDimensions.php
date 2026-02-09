<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_DIMS().
 *
 * Returns the dimensions of an array as a text string.
 *
 * @see https://www.postgresql.org/docs/9.6/static/functions-array.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_DIMENSIONS(e.array) FROM Entity e"
 */
class ArrayDimensions extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_dims(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
