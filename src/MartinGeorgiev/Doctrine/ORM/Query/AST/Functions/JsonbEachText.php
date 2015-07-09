<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class JsonbEachText extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('jsonb_each_text(%s)');
        $this->addLiteralMapping('StringPrimary');
    }
}