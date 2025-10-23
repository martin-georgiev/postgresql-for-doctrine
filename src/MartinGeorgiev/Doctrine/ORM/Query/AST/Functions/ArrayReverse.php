<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_REVERSE().
 *
 * @see https://www.postgresql.org/docs/18/functions-array.html
 * @since 3.6
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ArrayReverse extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_reverse(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
