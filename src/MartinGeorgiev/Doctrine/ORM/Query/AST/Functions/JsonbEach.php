<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class JsonbEach extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('jsonb_each(%s)');
        $this->addLiteralMapping('StringPrimary');
    }
}