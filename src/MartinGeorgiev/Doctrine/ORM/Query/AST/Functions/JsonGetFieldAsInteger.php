<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql json field retrieval as integer, filtered by key (using ->> and type casting to BIGINT)
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 *
 * @since 0.3
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonGetFieldAsInteger extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('CAST(%s ->> %s as BIGINT)');
        $this->addLiteralMapping('StringPrimary');
        $this->addLiteralMapping('StringPrimary');
    }
}
