<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_FILL().
 *
 * Creates an array filled with copies of the given value, with dimensions specified by the second argument.
 *
 * @see https://www.postgresql.org/docs/17/functions-array.html
 * @since 4.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ARRAY_FILL('x', ARRAY(3)) FROM Entity e" returns {x,x,x}
 */
class ArrayFill extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_fill(%s, %s)');
        $this->addNodeMapping('NewValue');
        $this->addNodeMapping('StringPrimary');
    }
}
