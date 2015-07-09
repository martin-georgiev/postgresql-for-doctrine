<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class Any extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('ANY(%s)');
        $this->addLiteralMapping('ArithmeticPrimary');
    }
}
