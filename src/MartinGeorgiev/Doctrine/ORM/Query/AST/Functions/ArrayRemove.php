<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql ARRAY_REMOVE()
 * @see https://www.postgresql.org/docs/9.4/static/functions-array.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ArrayRemove extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('array_remove(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('Literal');
    }
}
