<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class Arr extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('ARRAY[%s]');
        $this->addLiteralMapping('StringPrimary');
    }
}
