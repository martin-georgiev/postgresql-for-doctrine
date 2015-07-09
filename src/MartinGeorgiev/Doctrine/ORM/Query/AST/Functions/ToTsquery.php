<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class ToTsquery extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('to_tsquery(%s)');
        $this->addLiteralMapping('StringPrimary');
    }
}
