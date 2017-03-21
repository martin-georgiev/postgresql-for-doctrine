<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql JSON_STRIP_NULLS()
 * @see https://www.postgresql.org/docs/9.6/static/functions-array.html
 *
 * @since 0.10
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonStripNulls extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('json_strip_nulls(%s)');
        $this->addLiteralMapping('StringPrimary');
    }
}
