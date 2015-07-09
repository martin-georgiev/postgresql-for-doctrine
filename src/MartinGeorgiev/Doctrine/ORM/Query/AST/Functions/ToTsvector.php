<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class ToTsvector extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('to_tsvector(%s)');
        $this->addLiteralMapping('StringExpression');
    }
}