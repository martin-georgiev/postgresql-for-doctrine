<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class JsonGetFieldAsInteger extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('CAST(%s ->> %s as BIGINT)');
        $this->addLiteralMapping('StringPrimary');
        $this->addLiteralMapping('StringPrimary');
    }
}