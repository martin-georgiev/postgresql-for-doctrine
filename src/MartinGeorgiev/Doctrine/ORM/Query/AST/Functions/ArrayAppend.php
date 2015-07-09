<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class ArrayAppend extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('array_append(%s, %s)');
        $this->addLiteralMapping('StringPrimary');
        $this->addLiteralMapping('InputParameter');
    }
}