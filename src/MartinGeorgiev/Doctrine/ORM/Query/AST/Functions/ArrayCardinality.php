<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql CARDINALITY()
 * @see http://www.postgresql.org/docs/9.4/static/functions-array.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ArrayCardinality extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('cardinality(%s)');
        $this->addLiteralMapping('StringPrimary');
    }
}