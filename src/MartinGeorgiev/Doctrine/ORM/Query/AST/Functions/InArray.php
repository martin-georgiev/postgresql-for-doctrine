<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql STRING_TO_ARRAY()
 * @see https://www.postgresql.org/docs/9.4/static/functions-array.html
 *
 * @since 0.4
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InArray extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('%s = ANY(%s)');
        $this->addNodeMapping('InputParameter');
        $this->addNodeMapping('StringPrimary');
    }
}
