<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL ltree ? operator.
 *
 * Checks whether an ltree path matches any lquery pattern in an array of patterns.
 *
 * @see https://www.postgresql.org/docs/18/ltree.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with boolean comparison: "WHERE MATCHES_ANY_LQUERY(e.path, ARR('Top.*', 'A.*')) = TRUE"
 * Returns boolean, must be used with "= TRUE" or "= FALSE" when used in WHERE clause in DQL.
 */
class MatchesAnyLquery extends BaseFunction
{
    protected function customizeFunction(): void
    {
        // ARRAY[...] of untyped literals resolves to text[], for which no ? operator against ltree
        // exists. The cast target propagates into the constructor and coerces the elements to lquery.
        // The doubled ? escapes the PDO placeholder parser.
        $this->setFunctionPrototype('(%s ?? CAST(%s AS lquery[]))');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
