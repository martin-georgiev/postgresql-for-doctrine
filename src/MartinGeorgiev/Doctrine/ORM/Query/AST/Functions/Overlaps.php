<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql check if left side overlaps with right side (using &&)
 * @see https://www.postgresql.org/docs/9.6/static/functions-array.html
 *
 * @since 0.10
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Overlaps extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('(%s && %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
