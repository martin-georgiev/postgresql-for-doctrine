<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql ARRAY_TO_STRING()
 * @see http://www.postgresql.org/docs/9.4/static/functions-array.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ArrayToString extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('array_to_string(%s, %s)');
        $this->addLiteralMapping('StringPrimary');
        $this->addLiteralMapping('InputParameter');
    }
}