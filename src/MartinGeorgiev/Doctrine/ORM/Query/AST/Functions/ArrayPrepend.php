<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class ArrayPrepend extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('array_prepend(%s, %s)');
        $this->addLiteralMapping('StringPrimary');
        $this->addLiteralMapping('InputParameter');
    }
}