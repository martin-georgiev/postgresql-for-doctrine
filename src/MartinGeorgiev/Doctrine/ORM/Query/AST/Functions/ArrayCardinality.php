<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class ArrayCardinality extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('cardinality(%s)');
        $this->addLiteralMapping('StringPrimary');
    }
}