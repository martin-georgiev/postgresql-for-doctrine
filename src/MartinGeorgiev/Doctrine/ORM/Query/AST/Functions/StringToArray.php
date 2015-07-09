<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class StringToArray extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('string_to_array(%s, %s)');
        $this->addLiteralMapping('StringPrimary');
        $this->addLiteralMapping('InputParameter');
    }
}