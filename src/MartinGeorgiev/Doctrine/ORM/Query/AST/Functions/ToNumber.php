<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL to_number().
 *
 * @see https://www.postgresql.org/docs/current/functions-formatting.html
 */
class ToNumber extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('to_number(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
