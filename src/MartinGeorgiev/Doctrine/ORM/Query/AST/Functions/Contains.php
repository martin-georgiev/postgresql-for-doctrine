<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class Contains extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('(%s <@ %s)');
        $this->addLiteralMapping('ArithmeticPrimary');
        $this->addLiteralMapping('ArithmeticPrimary');
    }
}