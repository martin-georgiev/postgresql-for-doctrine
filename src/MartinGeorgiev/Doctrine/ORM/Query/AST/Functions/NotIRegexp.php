<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL !~* operator.
 *
 * Performs case-insensitive regular expression non-matching.
 *
 * @see https://www.postgresql.org/docs/9.3/functions-matching.html#FUNCTIONS-POSIX-REGEXP
 * @since 1.8
 *
 * @author Ian Jenkins <ian@jenko.me>
 *
 * @example Using it in DQL with boolean comparison: "WHERE NOT_IREGEXP(e.text, 'pattern') = TRUE"
 * Returns boolean, must be used with "= TRUE" or "= FALSE" when used in WHERE clause in DQL.
 */
class NotIRegexp extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s !~* %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
