<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql ANY()
 * @see http://www.postgresql.org/docs/9.4/static/functions-subquery.html#FUNCTIONS-SUBQUERY-ANY-SOME
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Any extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('ANY(%s)');
        $this->addLiteralMapping('StringPrimary');
    }
}
