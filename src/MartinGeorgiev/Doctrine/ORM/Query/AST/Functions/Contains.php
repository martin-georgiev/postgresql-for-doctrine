<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql check if left side contains right side (using @>)
 * @see https://www.postgresql.org/docs/9.4/static/functions-array.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Contains extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('(%s @> %s)');
        $this->addLiteralMapping('ArithmeticPrimary');
        $this->addLiteralMapping('ArithmeticPrimary');
    }
}
