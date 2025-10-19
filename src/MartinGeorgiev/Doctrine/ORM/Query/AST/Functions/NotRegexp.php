<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL case-sensitive not regular expression check (using !~).
 *
 * @see https://www.postgresql.org/docs/9.3/functions-matching.html#FUNCTIONS-POSIX-REGEXP
 * @since 1.8
 *
 * @author Ian Jenkins <ian@jenko.me>
 */
class NotRegexp extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s !~ %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
