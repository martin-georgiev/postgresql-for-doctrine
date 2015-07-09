<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class JsonbObjectKeys extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('jsonb_object_keys(%s)');
        $this->addLiteralMapping('StringPrimary');
    }
}