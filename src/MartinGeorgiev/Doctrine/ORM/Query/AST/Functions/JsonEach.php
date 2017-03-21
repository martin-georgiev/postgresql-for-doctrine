<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql JSON_EACH()
 * @see https://www.postgresql.org/docs/9.6/static/functions-json.html
 *
 * @since 0.10
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonEach extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('json_each(%s)');
        $this->addLiteralMapping('StringPrimary');
    }
}
