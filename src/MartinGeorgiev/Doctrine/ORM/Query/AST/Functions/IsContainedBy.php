<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql check if left side is contained by right side (using <@)
 * @see https://www.postgresql.org/docs/9.4/static/functions-array.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class IsContainedBy extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('(%s <@ %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
