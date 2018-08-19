<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql ARRAY_APPEND()
 * @see https://www.postgresql.org/docs/9.4/static/functions-array.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ArrayAppend extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('array_append(%s, %s)');
        $this->addLiteralMapping('StringPrimary');
        $this->addLiteralMapping('Literal');
    }
}
