<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql ARRAY_LENGTH()
 * @see http://www.postgresql.org/docs/9.4/static/functions-array.html
 *
 * @since 0.9
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ArrayLength extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('array_length(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('Literal');
    }
}
