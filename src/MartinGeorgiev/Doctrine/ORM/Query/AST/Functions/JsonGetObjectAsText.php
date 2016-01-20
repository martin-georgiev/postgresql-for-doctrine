<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql json object retrival as text, filtered by specific path (using #>>)
 * @see http://www.postgresql.org/docs/9.4/static/functions-json.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonGetObjectAsText extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('(%s #>> %s)');
        $this->addLiteralMapping('StringPrimary');
        $this->addLiteralMapping('StringPrimary');
    }
}