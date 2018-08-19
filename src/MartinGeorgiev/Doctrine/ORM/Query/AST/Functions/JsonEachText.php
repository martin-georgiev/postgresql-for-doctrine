<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql JSON_EACH_TEXT()
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 *
 * @since 0.10
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonEachText extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('json_each_text(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
