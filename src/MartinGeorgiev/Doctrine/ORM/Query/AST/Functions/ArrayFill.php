<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_FILL().
 *
 * @see https://www.postgresql.org/docs/17/functions-array.html
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
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

