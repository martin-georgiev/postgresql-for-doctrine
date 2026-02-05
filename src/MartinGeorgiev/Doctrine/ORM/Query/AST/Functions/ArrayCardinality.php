<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL CARDINALITY().
 *
 * Returns the number of elements in an array.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-array.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_CARDINALITY(e.array) FROM Entity e"
 */
class ArrayCardinality extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('cardinality(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
