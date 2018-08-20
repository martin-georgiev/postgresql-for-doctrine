<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql ARRAY_NDIMS()
 * @see http://www.postgresql.org/docs/9.6/static/functions-array.html
 *
 * @since 0.10
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ArrayNumberOfDimensions extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('array_ndims(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
