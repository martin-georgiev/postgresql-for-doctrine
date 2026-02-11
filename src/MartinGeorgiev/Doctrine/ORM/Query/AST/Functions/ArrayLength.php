<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_LENGTH().
 *
 * Returns the length of a specified dimension of an array.
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-array.html
 * @since 0.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_LENGTH(e.array, 1) FROM Entity e"
 */
class ArrayLength extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_length(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
    }
}
