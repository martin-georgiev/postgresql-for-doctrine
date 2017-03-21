<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql ROW_TO_JSON()
 * @see https://www.postgresql.org/docs/9.6/static/functions-json.html
 *
 * @since 0.10
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class RowToJson extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('row_to_json(%s)');
        $this->addLiteralMapping('StringPrimary');
    }
}
