<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class InArray extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('%s = ANY(%s)');
        $this->addLiteralMapping('InputParameter');
        $this->addLiteralMapping('StringPrimary');
    }
}
