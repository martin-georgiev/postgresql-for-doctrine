<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL STARTS_WITH().
 *
 * Checks if a string starts with a specified prefix.
 *
 * @see https://www.postgresql.org/docs/17/functions-string.html
 * @since 2.3
 *
 * @author Alexander Dmitryuk <xakzona@bk.ru>
 *
 * @example Using it in DQL with boolean comparison: "WHERE STARTS_WITH(e.text, 'prefix') = TRUE"
 * Returns boolean, must be used with "= TRUE" or "= FALSE" in DQL.
 */
class StartsWith extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(STARTS_WITH(%s, %s))');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
