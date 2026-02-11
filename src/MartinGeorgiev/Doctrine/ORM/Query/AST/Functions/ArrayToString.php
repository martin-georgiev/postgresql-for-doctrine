<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_TO_STRING().
 *
 * Converts an array to a string with a specified delimiter.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-array.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_TO_STRING(e.array, ',') FROM Entity e"
 */
class ArrayToString extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_to_string(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
