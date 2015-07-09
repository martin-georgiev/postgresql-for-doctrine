<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class ArrayToString extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('array_to_string(%s, %s)');
        $this->addLiteralMapping('StringPrimary');
        $this->addLiteralMapping('InputParameter');
    }
}