<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql JSONB_SET()
 * @see https://www.postgresql.org/docs/9.6/static/functions-array.html
 *
 * @since 0.10
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonbSet extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('jsonb_set(%s, %s, %s)');
        $this->addLiteralMapping('StringPrimary');
        $this->addLiteralMapping('StringPrimary');
        $this->addLiteralMapping('StringPrimary');
    }
}
