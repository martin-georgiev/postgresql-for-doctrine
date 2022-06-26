<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSql case-sensitive regular expression check (using ~).
 *
 * @see https://www.postgresql.org/docs/9.3/functions-matching.html#FUNCTIONS-POSIX-REGEXP
 * @since 1.8
 *
 * @author Ian Jenkins <ian@jenko.me>
 */
class Regexp extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('(%s ~ %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
