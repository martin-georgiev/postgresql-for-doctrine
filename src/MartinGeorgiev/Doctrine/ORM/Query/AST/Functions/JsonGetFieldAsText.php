<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class JsonGetFieldAsText extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('(%s ->> %s)');
        $this->addLiteralMapping('StringPrimary');
        $this->addLiteralMapping('StringPrimary');
    }
}