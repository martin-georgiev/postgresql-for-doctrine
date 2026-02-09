<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_CAT().
 *
 * Concatenates two arrays.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-array.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_CAT(e.array1, e.array2) FROM Entity e"
 */
class ArrayCat extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_cat(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
