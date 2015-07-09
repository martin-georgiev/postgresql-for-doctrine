<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class All extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('ALL(%s)');
        $this->addLiteralMapping('ArithmeticPrimary');
    }
}
