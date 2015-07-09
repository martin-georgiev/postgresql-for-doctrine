<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class JsonbArrayElementsText extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('jsonb_array_elements_text(%s)');
        $this->addLiteralMapping('StringPrimary');
    }
}