<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL to_timestamp().
 *
 * @see https://www.postgresql.org/docs/current/functions-formatting.html
 */
class ToTimestamp extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('to_timestamp(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
