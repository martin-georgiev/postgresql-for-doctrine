<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class JsonbExists extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('jsonb_exists(%s, %s)');
        $this->addLiteralMapping('StringPrimary');
        $this->addLiteralMapping('InputParameter');
    }
}