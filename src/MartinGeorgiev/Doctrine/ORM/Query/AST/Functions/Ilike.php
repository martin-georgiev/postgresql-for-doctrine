<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ILIKE().
 *
 * Performs case-insensitive pattern matching.
 *
 * @see https://www.postgresql.org/docs/9.3/functions-matching.html
 * @since 1.1
 *
 * @author llaakkkk <lenakirichokv@gmail.com>
 *
 * @example Using it in DQL with boolean comparison: "WHERE ILIKE(e.name, '%john%') = TRUE"
 * Returns boolean, must be used with "= TRUE" or "= FALSE" when used in WHERE clause in DQL.
 */
class Ilike extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('%s ilike %s');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
