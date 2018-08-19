<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql check if two arrays have elements in common (using &&)
 * @see https://www.postgresql.org/docs/9.4/static/functions-array.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @deprecated Deprecated since v0.12 and will be removed in v1.0. Use Overlaps instead.
 * @codeCoverageIgnore 
 */
class ArrayAreOverlapingEachOther extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('(%s && %s)');
        $this->addLiteralMapping('StringPrimary');
        $this->addLiteralMapping('StringPrimary');
    }
}
