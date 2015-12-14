<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class Least extends Greatest
{
    /**
     * {@inheritDoc}
     */
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('LEAST(%s)');
    }
}
