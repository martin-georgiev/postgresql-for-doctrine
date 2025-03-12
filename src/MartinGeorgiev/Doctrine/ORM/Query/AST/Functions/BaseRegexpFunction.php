<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

abstract class BaseRegexpFunction extends BaseFunction
{
    abstract protected function getFunctionName(): string;

    abstract protected function getParameterCount(): int;

    protected function customizeFunction(): void
    {
        $parameters = \str_repeat(', %s', $this->getParameterCount() - 1);
        $this->setFunctionPrototype($this->getFunctionName().'(%s'.$parameters.')');

        for ($i = 0; $i < $this->getParameterCount(); $i++) {
            $this->addNodeMapping('StringPrimary');
        }
    }
}
