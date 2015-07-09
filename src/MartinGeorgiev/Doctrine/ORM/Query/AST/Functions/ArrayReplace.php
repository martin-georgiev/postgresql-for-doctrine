<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class ArrayReplace extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('array_replace(%s, %s, %s)');
        $this->addLiteralMapping('StringPrimary');
        $this->addLiteralMapping('InputParameter');
        $this->addLiteralMapping('InputParameter');
    }
}