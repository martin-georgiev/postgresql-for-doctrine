<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class JsonbArrayElements extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('jsonb_array_elements(%s)');
        $this->addLiteralMapping('StringPrimary');
    }
}