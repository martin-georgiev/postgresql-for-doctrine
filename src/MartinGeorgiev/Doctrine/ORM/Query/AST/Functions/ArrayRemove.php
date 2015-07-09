<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class ArrayRemove extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('array_remove(%s, %s)');
        $this->addLiteralMapping('StringPrimary');
        $this->addLiteralMapping('InputParameter');
    }
}