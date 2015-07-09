<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class JsonbArrayLength extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('jsonb_array_length(%s)');
        $this->addLiteralMapping('StringPrimary');
    }
}