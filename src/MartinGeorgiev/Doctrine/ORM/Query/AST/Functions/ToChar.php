<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL to_char().
 *
 * @see https://www.postgresql.org/docs/current/functions-formatting.html
 */
class ToChar extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('to_char(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
