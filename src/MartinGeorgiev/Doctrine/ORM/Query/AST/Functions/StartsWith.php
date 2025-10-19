<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class StartsWith extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(STARTS_WITH(%s, %s))');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
