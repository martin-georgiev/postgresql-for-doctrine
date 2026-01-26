<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ARRAY_UPPER().
 *
 * @see https://www.postgresql.org/docs/17/functions-array.html
 * @since 4.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ArrayUpper extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_upper(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
    }
}
