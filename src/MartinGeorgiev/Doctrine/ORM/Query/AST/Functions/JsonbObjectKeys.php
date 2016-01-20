<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql JSONB_OBJECT_KEYS()
 * @see http://www.postgresql.org/docs/9.4/static/functions-json.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonbObjectKeys extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('jsonb_object_keys(%s)');
        $this->addLiteralMapping('StringPrimary');
    }
}