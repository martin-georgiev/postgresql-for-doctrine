<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql ARRAY_REPLACE()
 * @see https://www.postgresql.org/docs/9.4/static/functions-array.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ArrayReplace extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('array_replace(%s, %s, %s)');
        $this->addLiteralMapping('StringPrimary');
        $this->addLiteralMapping('InputParameter');
        $this->addLiteralMapping('InputParameter');
    }
}
