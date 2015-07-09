<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class ArrayCat extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('array_cat(%s, %s)');
        $this->addLiteralMapping('StringPrimary');
        $this->addLiteralMapping('StringPrimary');
    }
}