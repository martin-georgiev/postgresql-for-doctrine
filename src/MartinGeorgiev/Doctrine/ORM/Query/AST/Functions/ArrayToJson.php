<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql ARRAY_TO_JSON()
 * @see https://www.postgresql.org/docs/9.6/static/functions-json.html
 *
 * @since 0.10
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ArrayToJson extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('array_to_json(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
