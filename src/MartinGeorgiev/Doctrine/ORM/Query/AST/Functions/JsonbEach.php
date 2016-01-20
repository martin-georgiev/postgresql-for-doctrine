<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql JSONB_EACH()
 * @see http://www.postgresql.org/docs/9.4/static/functions-json.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonbEach extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('jsonb_each(%s)');
        $this->addLiteralMapping('StringPrimary');
    }
}