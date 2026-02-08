<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ~* operator.
 *
 * Performs case-insensitive regular expression matching.
 *
 * @see https://www.postgresql.org/docs/9.3/functions-matching.html#FUNCTIONS-POSIX-REGEXP
 * @since 1.8
 *
 * @author Ian Jenkins <ian@jenko.me>
 *
 * @example Using it in DQL: "SELECT e.id FROM Entity e WHERE IREGEXP(e.text, 'pattern') = TRUE"
 */
class IRegexp extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s ~* %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
