<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql ARRAY[]
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Arr extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('ARRAY[%s]');
        $this->addLiteralMapping('StringPrimary');
    }
}
