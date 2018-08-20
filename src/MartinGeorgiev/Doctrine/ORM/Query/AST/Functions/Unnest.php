<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql UNNEST() for single array argument
 * @see http://www.postgresql.org/docs/9.6/static/functions-array.html
 *
 * @since 0.10
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Unnest extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('unnest(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
